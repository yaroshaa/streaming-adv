<?php

namespace App\Console\Commands\Module;

use App\Services\Modules\JsAssetPublisher;
use Illuminate\Console\Command;
use Symfony\Component\Finder\Finder;

class PublishCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:publish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish modules';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(JsAssetPublisher $assetPublisher)
    {
        $assetPublisher->cleanUpSymlinks();

        $modules = (new Finder())
            ->in(base_path('modules'))
            ->depth(0)
            ->directories();

        foreach ($modules as $module) {
            $assetPublisher->publish($module->getBasename());
        }

        /// Visual timeout for compile assets +-
        $count = 5;
        $bar = $this->output->createProgressBar($count);
        $bar->start();
        for ($i = 1; $i <= $count; $i++) {
            sleep(1);
            $bar->advance();
        }
        $bar->finish();
        $this->info("\nSuccess");
        return 0;
    }
}
