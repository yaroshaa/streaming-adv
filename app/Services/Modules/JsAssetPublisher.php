<?php


namespace App\Services\Modules;

use Illuminate\Filesystem\Filesystem;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

class JsAssetPublisher
{
    const PATH_TO_ELEMENT_COMPONENT = 'resources/js/element/components';
    const PATH_FROM_ELEMENT_COMPONENT = 'modules/{moduleName}/js/element';
    const PLACEHOLDER_MODULE_NAME = '{moduleName}';

    private Filesystem $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * Unlink broken symlinks
     */
    public function cleanUpSymlinks(): void
    {
        $brokenSymLinks = (new Finder())
            ->in(base_path(self::PATH_TO_ELEMENT_COMPONENT))
            ->filter(function (SplFileInfo $file) {
                return $file->isLink() && empty($file->getRealPath());
            });

        foreach ($brokenSymLinks as $links) {
            unlink($links->getPathname());
        }
    }

    public function publish(string $moduleName)
    {
        $this->publishElementAssets($moduleName);
    }

    private function publishElementAssets(string $moduleName): void
    {
        $source = base_path(str_replace(
            self::PLACEHOLDER_MODULE_NAME,
            $moduleName,
            self::PATH_FROM_ELEMENT_COMPONENT
        ));

        $destination = base_path(self::PATH_TO_ELEMENT_COMPONENT . DIRECTORY_SEPARATOR . $moduleName);
        if (is_dir($source)) {
            $this->filesystem->makeRelativeLink($source, $destination);
        }
    }
}
