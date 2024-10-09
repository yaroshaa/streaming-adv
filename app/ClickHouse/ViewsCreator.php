<?php


namespace App\ClickHouse;


use ClickHouseDB\Client;
use Generator;
use Illuminate\Support\Facades\Config;

class ViewsCreator
{
    /**
     * @var View[]
     */
    private array $views = [];
    /**
     * @var Client
     */
    private Client $client;

    /**
     * ViewsCreator constructor.
     * @param Client $client
     * @param View ...$views
     */
    public function __construct(Client $client, View ...$views)
    {
        $this->client = $client;
        $this->views = $views;
    }

    /**
     * @param bool $rewrite
     * @return Generator
     * @throws \Exception
     */
    public function init(bool $rewrite = false): Generator
    {
        foreach ($this->views as $view) {
            $exists = $this->client->isExists(Config::get('clickhouse.dbname'), $view::getName());

            if ($exists && !$rewrite) {
                yield sprintf('View %s is already exists. To modify use --rewrite=true option', $view::getName());

                continue;
            }

            if ($exists) {
                $this->client->write(sprintf('DROP TABLE %s', $view::getName()));
            }

            $this->client->write($view->getQuery());

            yield sprintf('View %s %s', $view::getName(), $rewrite ? 'updated' : 'created');
        }
    }
}