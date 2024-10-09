<?php

namespace Modules\Feedbacks\Http\Controllers;

use Exception;
use Illuminate\Http\Response;
use Modules\Feedbacks\Http\Requests\FacebookWebhookRequest;
use Modules\Feedbacks\Services\Facebook\FacebookWebhookService;

/**
 * Class FacebookWebhookController
 * @package Modules\Feedbacks\Http\Controllers
 */
class FacebookWebhookController
{
    /**
     * Handle the incoming request.
     *
     * @param FacebookWebhookRequest $request
     * @param FacebookWebhookService $facebookWebhookService
     * @return Response
     * @throws Exception
     */
    public function __invoke(FacebookWebhookRequest $request, FacebookWebhookService $facebookWebhookService): Response
    {
        $facebookWebhookService->process();

        return response('ok');
    }
}
