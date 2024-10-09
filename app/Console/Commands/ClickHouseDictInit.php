<?php

namespace App\Console\Commands;

use App\ClickHouse\SchemaUpdater;
use Illuminate\Console\Command;

class ClickHouseDictInit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clickhouse:dict:init {--R|rewrite=false}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Init clickhouse dictionaries';

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
     * @param SchemaUpdater $schemaUpdater
     * @return int
     */
    public function handle(SchemaUpdater $schemaUpdater)
    {
        $regenerate = $this->option('rewrite') === 'false'
            ? false
            : (
                $this->option('rewrite') === 'true'
                    ? true
                    : null
            );

        foreach ($schemaUpdater->dictInit($regenerate) as $message) {
            $this->info($message);
        };

        $this->info('All dictionaries initiated');

        return 0;
    }
}
