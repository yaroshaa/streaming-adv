<?php

namespace Modules\Feedbacks\Commands\Sync;

use Modules\Feedbacks\Services\Facebook\Api\ApplicationService;
use Modules\Feedbacks\Services\Facebook\FacebookService;
use Illuminate\Console\Command;

/**
 * Class FacebookCommand
 * @package Modules\Feedbacks\Console\Commands
 */
class FacebookCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'feedbacks:sync:facebook';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync ads comments from facebook';

    /**
     * Execute the console command.
     *
     * @param FacebookService $facebookService
     * @return int
     */
    public function handle(FacebookService $facebookService)
    {
        foreach ($facebookService->getApplicationServicesAsGenerator() as $applicationService) {
            /** @var ApplicationService $applicationService */
            $applicationService->processFeedbacks();
        }

        return 0;
    }
}
