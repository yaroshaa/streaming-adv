<?php

namespace App\Console\Commands\Analytics;

use App\ClickHouse\Models\AnalyticsEvents;
use App\Entities\AnalyticsSite;
use App\Services\AnalyticsService;
use Doctrine\ORM\EntityManager;
use Faker\Factory;
use Illuminate\Console\Command;
use Modules\Analytic\ClickHouse\Repositories\AnalyticsRepository;
use Modules\Analytic\Events\AnalyticEvent;
use Modules\Analytic\Services\AnalyticService;

class GenerateDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'analytics:generate {--count-events=} {--live} {--timeout=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate tests analytics data';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param EntityManager $entityManager
     * @param AnalyticsService $analyticsService
     * @param AnalyticsRepository $analyticsRepository
     * @return int
     * @throws \App\ClickHouse\ClickHouseException
     */
    public function handle(EntityManager $entityManager, AnalyticsService $analyticsService, AnalyticsRepository $analyticsRepository)
    {
        if ($this->option('live')) {
            $this->liveGenerateData($entityManager, $analyticsService, $analyticsRepository);
        } else {
            $this->generateData($entityManager, $analyticsService);
        }

        return 0;
    }

    /**
     * @param EntityManager $entityManager
     * @param AnalyticsService $analyticsService
     */
    private function generateData(EntityManager $entityManager, AnalyticsService $analyticsService)
    {
        $faker = Factory::create();

        try {

            $countEvents = $this->option('count-events');
            $patches = $this->getRandPatches();

            $sessionId = AnalyticsEvents::guidv4();
            $randEventsForOneSession = rand(1, 10);
            $date = $faker->dateTimeBetween('-20 days', '+1 day');

            for ($i = 0; $i < $countEvents; $i++) {
                /**
                 * @var AnalyticsSite $site
                 */
                $site = $this->getRandomSites($entityManager);

                $this->output->write($randEventsForOneSession . ":" . $sessionId . "\n");
                if ($i % $randEventsForOneSession === 0) {
                    $sessionId = AnalyticsEvents::guidv4();
                    $randEventsForOneSession = rand(1, 10);
                    $date = $faker->dateTimeBetween('-20 days', '+1 day');
                }

                $patchIndex = rand(0, count($patches) - 1);

                $event = $this->getRandomEvent();

                if ($event === 'pageview') {
                    $analyticsService->track($site, $event, [
                        'patch' => $patches[$patchIndex]
                    ], $date, $sessionId);
                } else {
                    $analyticsService->track($site, $event, [], $date, $sessionId);
                }
            }
        } catch (\Exception $e) {
            $this->output->writeln("\033[31m " . $e->getMessage() . "\033[0m");
        }
    }

    /**
     * @param EntityManager $entityManager
     * @param AnalyticsService $analyticsService
     * @param AnalyticsRepository $analyticsRepository
     * @throws \App\ClickHouse\ClickHouseException
     */
    private function liveGenerateData(EntityManager $entityManager, AnalyticsService $analyticsService, AnalyticsRepository $analyticsRepository)
    {
        $patches = $this->getRandPatches();
        $sessionId = AnalyticsEvents::guidv4();
        $randEventsForOneSession = rand(1, 10);

        $i = 0;
        while (true) {

            /**
             * @var AnalyticsSite $site
             */
            $site = $this->getRandomSites($entityManager);

            if ($i % $randEventsForOneSession === 0) {
                $sessionId = AnalyticsEvents::guidv4();
                $randEventsForOneSession = rand(1, 10);
            }

            $patchIndex = rand(0, count($patches) - 1);

            $event = $this->getRandomEvent();
            $date = new \DateTime();

            if ($event === 'pageview') {
                $analyticsService->track($site, $event, [
                    'patch' => $patches[$patchIndex]
                ], $date, $sessionId);
            } else {
                $analyticsService->track($site, $event, [], $date, $sessionId);
            }

            $data = AnalyticService::getLast15MinutesConversionRate($entityManager, $analyticsRepository);
            AnalyticEvent::dispatch($data);

            $i++;

            $this->output->writeln('Session: ' . $randEventsForOneSession . ' ' . $date->format('Y-m-d H:i:s'));
            if ($this->option('timeout')) {
                usleep($this->option('timeout'));
            } else {
                usleep(1000000);
            }
        }
    }

    /**
     * Get random event
     *
     * @return mixed
     */
    private function getRandomEvent()
    {
        $events = [
            'click',
            'pageview'
        ];

        $randIndex = rand(0, count($events) - 1);

        return $events[$randIndex];
    }

    /**
     * Get rand patches.
     *
     * @return array
     */
    private function getRandPatches()
    {
        $faker = Factory::create();

        $patches = [];
        for ($i = 0; $i < 50; $i++) {
            $patches[] = '/' . $faker->regexify('[A-Za-z0-9]{20}');
        }

        return $patches;
    }

    /**
     * @param EntityManager $entityManager
     * @return mixed|object
     */
    private function getRandomSites(EntityManager $entityManager)
    {
        $repository = $entityManager->getRepository(AnalyticsSite::class);
        $sites = $repository->findAll();
        $index = rand(1, count($sites) - 1);

        return $sites[$index];
    }
}
