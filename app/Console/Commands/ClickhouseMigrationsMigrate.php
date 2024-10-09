<?php

namespace App\Console\Commands;

use App\ClickHouse\Migrator;
use Exception;
use Illuminate\Console\Command;

class ClickhouseMigrationsMigrate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clickhouse:migrations:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(Migrator $migrator)
    {
        try {
            foreach ($migrator->migrate() as $message) {
                $this->info($message);
            }
        } catch (Exception $exception) {
            $this->error($exception->getMessage());
        }
        return 0;
    }
}
