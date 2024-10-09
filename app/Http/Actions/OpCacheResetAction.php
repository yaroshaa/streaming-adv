<?php

namespace App\Http\Actions;

use App\Http\Requests\OpCacheResetActionRequest;
use Illuminate\Http\JsonResponse;

class OpCacheResetAction
{
    public function __invoke(OpCacheResetActionRequest $request): JsonResponse
    {
        $response = [];
        if (function_exists('opcache_reset')) {
            $response['Result'] = opcache_reset() ? 'Yes' : 'No';
        } else {
            $response['Result'] = 'Function "opcache_reset" not exist';
        }

        return response()->json($response);
    }
}
