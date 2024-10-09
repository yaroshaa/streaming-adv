<?php

namespace App\Console\Commands;

use App\ClickHouse\Migrator;
use Exception;
use Illuminate\Console\Command;

class ClickhouseMigrationsRollback extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clickhouse:migrations:rollback {version?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rolling back migrations';

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
            foreach ($migrator->rollback($this->argument('version')) as $message) {
                $this->info($message);
            }
        } catch (Exception $exception) {
            $this->error($exception->getMessage());
        }
        return 0;
    }
}
