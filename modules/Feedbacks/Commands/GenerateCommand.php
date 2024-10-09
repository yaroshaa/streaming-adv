<?php

namespace Modules\Feedbacks\Commands;

use App\ClickHouse\ClickHouseException;
use Modules\Feedbacks\Services\Mock\FeedbackMockService;
use Illuminate\Console\Command;

/**
 * Class GenerateMockFeedbackCommand
 * @package Modules\Feedbacks\Console\Commands
 */
class GenerateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'feedbacks:generate {--count=100} {--delay=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate feedbacks';

    /**
     * Execute the console command.
     *
     * @param FeedbackMockService $feedbackMockService
     * @return int
     * @throws ClickHouseException
     */
    public function handle(FeedbackMockService $feedbackMockService): int
    {
        $count = $this->option('count');
        $bar = $this->output->createProgressBar($count);
        $bar->start();
        for ($i = 1; $i <= $count; $i++) {
            sleep($this->option('delay'));
            $feedbackMockService->execute();
            $bar->advance();
        }
        $bar->finish();
        $this->info("\nSuccess");
        return 0;
    }
}
