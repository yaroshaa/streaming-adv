<?php

namespace App\Console\Commands;

use App\ClickHouse\ViewsCreator;
use Illuminate\Console\Command;

class ClickhouseViewsInit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clickhouse:views:init {--R|rewrite=false}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clickhouse views init';

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
    public function handle(ViewsCreator $creator)
    {
        $rewrite = $this->option('rewrite') === 'false'
            ? false
            : $this->option('rewrite') === 'true';

        foreach ($creator->init($rewrite) as $message) {
            $this->info($message);
        }

        $this->info('All views initiated');

        return 0;
    }
}
