<?php

namespace App\Mixins;

use Symfony\Component\Filesystem\Filesystem;
use Closure;

class FilesystemMixin
{
    public function makePathRelative(): Closure
    {
        return function (string $path, string $onPath) {
            return (new Filesystem)->makePathRelative($path, $onPath);
        };
    }

    public function getRelativePath(): Closure
    {
        return function (string $from, string $to) {
            return $this->makePathRelative(
                    dirname($from),
                    dirname($to)
                ) . basename($from);
        };
    }

    public function getRelativePathFromRoot(): Closure
    {
        return function (string $path) {
            return $this->makePathRelative(
                    dirname($path),
                    base_path()
                ) . basename($path);
        };
    }

    public function makeRelativeLink(): Closure
    {
        return function (string $from, string $to) {
            $pathTo = $this->getRelativePathFromRoot($to);
            if (is_link($pathTo)) {
                unlink($pathTo);
            }

            $this->link($this->getRelativePath($from, $to), $pathTo);
        };
    }
}
