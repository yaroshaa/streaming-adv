<?php

namespace Modules\Feedbacks\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Modules\Feedbacks\Http\Requests\HelpScoutWebhookRequest;
use Modules\Feedbacks\Services\HelpScout\HelpScoutWebhookService;

class HelpScoutWebhookController
{
    public function __invoke(HelpScoutWebhookRequest $request, HelpScoutWebhookService $webhookService): JsonResponse
    {
        $webhookService->process();

        return response()->json([
            'ok'
        ]);
    }
}
