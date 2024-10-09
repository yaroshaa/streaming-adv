<?php

namespace Modules\ModuleName\Http\Controllers;

use Modules\ModuleName\Http\Requests\ModuleNameRequest;
use Modules\ModuleName\Http\Resources\ModuleNameResource;
use Modules\ModuleName\Services\ModuleNameService;

class ModuleNameController
{
    public function __invoke(ModuleNameRequest $request, ModuleNameService $service): ModuleNameResource
    {
        $service->setFilter($request->all());
        return new ModuleNameResource($service);
    }
}
