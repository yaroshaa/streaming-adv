<?php


namespace App\ClickHouse;


use ClickHouseDB\Client;
use DateTime;
use Exception;
use Generator;
use Illuminate\Support\Facades\Config;

class Migrator
{
    private const TEMPLATE = '
<?php

class %migration_name% implements \App\ClickHouse\Migration
{
    public function up(\ClickHouseDB\Client $client): void
    {
        %queries_up%
    }

    public function down(\ClickHouseDB\Client $client): void
    {
        %queries_down%
    }
}
';
    private ?string $path = null;

    /**
     * @var SchemaUpdater
     */
    private SchemaUpdater $schemaUpdater;
    /**
     * @var ClickhouseConfig
     */
    private ClickhouseConfig $clickhouseConfig;
    /**
     * @var Client
     */
    private Client $client;

    /**
     * Migrator constructor.
     * @param SchemaUpdater $schemaUpdater
     * @param ClickhouseConfig $clickhouseConfig
     * @param Client $client
     */
    public function __construct(SchemaUpdater $schemaUpdater, ClickhouseConfig $clickhouseConfig, Client $client)
    {
        $this->schemaUpdater = $schemaUpdater;
        $this->clickhouseConfig = $clickhouseConfig;
        $this->client = $client;
    }

    public function diff()
    {
        $migrationName = 'Migration' . time();
        $queriesUp = [];
        $queriesDown = [];

        foreach ($this->clickhouseConfig->getModels() as $model) {
            $queriesUp[] = $this->schemaUpdater->getSql($model);
            $queriesDown[] = $this->schemaUpdater->revertSql($model);
        }

        $fileBody = str_replace([
            '%migration_name%',
            '%queries_up%',
            '%queries_down%'
        ], [
            $migrationName,
            implode("\n", array_map(fn($q) => sprintf('$client->write("' . $q . '");'), array_filter($queriesUp))),
            implode("\n", array_map(fn($q) => sprintf('$client->write("' . $q . '");'), array_filter($queriesDown))),
        ], self::TEMPLATE);

        return $this->writeFile($migrationName, $fileBody);
    }

    public function migrate(): Generator
    {
        if (!$this->client->isExists(Config::get('clickhouse.dbname'), 'migrations')) {
            $this->client->write('CREATE TABLE migrations (ver UInt64, created_at DateTime) ENGINE MergeTree() ORDER BY ver');
        }

        $path = $this->getPath();
        $files = array_diff(scandir($path), ['.', '..']);

        $migrated = array_map(fn($row) => $row['ver'], $this->client->select('
            SELECT ver
                FROM migrations
                GROUP BY ver
        ')->rows());

        $migratedCnt = 0;
        foreach ($files as $migrationFile) {
            $version = preg_replace('/Migration(\d+)\.php/', '$1', $migrationFile);

            if (in_array($version, $migrated)) {
                continue;
            }

            $migration = $this->getMigration($version);

            try {
                $migration->up($this->client);
                $this->client->insertAssocBulk('migrations', [[
                    'ver' => $version,
                    'created_at' => new DateTime()
                ]]);
            } catch (Exception $exception) {
                throw new Exception(sprintf('Error occurred while migrating %s: %s', $version, $exception->getMessage()));
            }

            $migratedCnt++;

            yield sprintf('Migration %d migrated', $version);
        }

        if (0 === $migratedCnt) {
            yield 'Already up to date';
        }
    }

    public function rollback(string $version = null): Generator
    {
        if (null === $version) {
            $latest = $this->client->select('
                SELECT ver, created_at
                FROM migrations
                ORDER BY ver DESC
                LIMIT 1;
            ')->fetchOne();

            if (!$latest) {
                return yield 'Nothing to rollback';
            }

            $migration = $this->getMigration($latest['ver']);

            $this->migrateDown($migration, $latest);

            return yield sprintf('Migration %s rolled back', $version);
        }

        $migrations = $this->client->select('
            SELECT ver, created_at
            FROM migrations
            WHERE ver >= (
                SELECT ver
                FROM (
                      SELECT ver
                      FROM migrations
                      WHERE ver = :ver
                      ORDER BY ver DESC
                      LIMIT 1
                         )
            )
            ORDER BY ver DESC;
          ', ['ver' =>  $version])->rows();

       if (!$migrations) {
            return yield 'Nothing to rollback';
        }

        foreach ($migrations as $migrationRow) {
            $migration = $this->getMigration($migrationRow['ver']);
            $this->migrateDown($migration, $migrationRow);

            yield sprintf('Migration %s rolled back', $migrationRow['ver']);
        }

        yield sprintf('Rolled back to %s', $version);
    }

    private function getPath(): string
    {
        if (null === $this->path) {
            $path = Config::get('clickhouse.migrations_path');

            if (!$path) {
                throw new ClickHouseException('Clickhouse migrations path not set. Please provide clickhouse.migrations_path config');
            }

            if (!is_dir($path)) {
                $result = mkdir($path);
                if (!$result) {
                    throw new ClickHouseException(sprintf('Can not create migrations directory %s', $path));
                }
            }

            $this->path = $path;
        }

        return $this->path;
    }


    private function writeFile(string $filename, string $body): string
    {
        $path = $this->getPath();

        if (!is_writable($path)) {
            throw new ClickHouseException(sprintf('Migrations directory %s is now writable', $path));
        }

        if (!file_put_contents($path . DIRECTORY_SEPARATOR . $filename . '.php', $body)) {
            throw new ClickHouseException('Can not write migration file');
        }

        return sprintf('Migration %s created', $filename);
    }

    /**
     * @param string $version
     * @return Migration
     * @throws ClickHouseException
     */
    private function getMigration(string $version)
    {
        $path = $this->getPath();

        $migrationName = 'Migration' . $version;

        $filename = $path . DIRECTORY_SEPARATOR . $migrationName . '.php';

        if (!file_exists($filename)) {
            throw new ClickHouseException(sprintf('Can not find migration file for %s', $version));
        }

        require $filename;

        if (!class_exists($migrationName)) {
            throw new ClickHouseException(sprintf('Clickhouse version class %s does not exist', $migrationName));
        }

        $migration = new $migrationName;

        if (!$migration instanceof Migration) {
            throw new ClickHouseException(sprintf('Migration class should implement %s', Migration::class));
        }

        return $migration;
    }

    /**
     * @param Migration $migration
     * @param array $migrationRow
     * @throws Exception
     */
    private function migrateDown(Migration $migration, array $migrationRow): void
    {
        try {
            $migration->down($this->client);
            $this->client->write('ALTER TABLE migrations DELETE where ver = :ver', ['ver' => $migrationRow['ver']]);
        } catch (Exception $exception) {
            throw new Exception(sprintf('Error occurred while rolling back %s: %s', $migrationRow['ver'], $exception->getMessage()));
        }
    }
}
