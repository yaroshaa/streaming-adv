<?php

namespace App\Console\Commands;

use App\ClickHouse\Migrator;
use Illuminate\Console\Command;

class ClickhouseMigrationsDiff extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clickhouse:migrations:diff';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates migrations diff';

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
        $this->info($migrator->diff());
        return 0;
    }
}
