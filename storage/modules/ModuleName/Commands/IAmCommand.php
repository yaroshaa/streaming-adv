<?php

namespace Modules\ModuleName\Commands;

use Illuminate\Console\Command;

class IAmCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module-name:iam';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Example';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->info('Iam, ModuleName');
        return 0;
    }
}
