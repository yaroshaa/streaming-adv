<?php

namespace App\Console\Commands\Module;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class GenerateCommand extends Command
{
    const PATH_TO_EXAMPLE_MODULE = 'storage/modules/ModuleName';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate module';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $moduleName = $this->ask('Write module name in CamelCase:');
        if (file_exists(base_path('modules/' . $moduleName))) {
            $this->warn(sprintf('Module with name %s exist', $moduleName));
            return 0;
        }
        $destination = base_path('modules/' . $moduleName);
        $filesystem = new Filesystem();
        $filesystem->copyDirectory(base_path(self::PATH_TO_EXAMPLE_MODULE), $destination);
        foreach ($filesystem->allFiles($destination) as $file) {
            file_put_contents($file->getPathname(), str_replace([
                'ModuleName',
                'modulename',
                'module-name',
            ], [
                $moduleName,
                Str::lower($moduleName),
                Str::kebab($moduleName)
            ], file_get_contents($file->getPathname())));

            $filesystem->move($file->getPathname(), str_replace([
                'ModuleName',
                'modulename',
            ], [
                $moduleName,
                Str::lower($moduleName),
            ], $file->getPathname()));
        }

        // @todo tailwind, vue, and another options
        //$createServiceProvider = $this->confirm('Create service provider?', true);
        //$createVueComponent = $this->confirm('Create vue component?', true);

        $this->info(sprintf('Successful your module "' . $moduleName . '" created.'));
        $this->info(
            sprintf(
                'Also need run php artisan module:publish, and go to http://127.0.0.1:8000/#/dashboard/%s',
                Str::kebab($moduleName))
        );
        return 0;
    }
}
