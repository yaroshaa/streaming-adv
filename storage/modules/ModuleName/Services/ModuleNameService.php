<?php

namespace Modules\ModuleName\Services;

class ModuleNameService
{
    private array $filter;

    public function setFilter(array $filter)
    {
        $this->filter = $filter;
    }

    public function calculate(): int
    {
        return $this->filter['number'] * config('modulename.number');
    }
}
