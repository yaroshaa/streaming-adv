<?php

namespace App\Console\Commands\Fake;

use Carbon\Carbon;
use Illuminate\Console\Command;

class LiveModeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fake:live-mode {--market=} {--date=now} {--count-min=1} {--count-max=10}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate fake data on real time';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        while (true) {
            $date = Carbon::create($this->option('date'));
            $count = rand($this->option('count-min'), $this->option('count-max'));

            $this->call('fake:order', [
                '--date' => $date->format('Y-m-d H:i:s'),
                '--count' => $count,
                '--market' => $this->option('market')
            ]);

            $this->call('fake:cart-action', [
                '--date' => $date->format('Y-m-d H:i:s'),
                '--count' => $count,
                '--market' => $this->option('market')
            ]);

            $this->call('fake:active-user', [
                '--date' => $date->format('Y-m-d H:i:s'),
                '--count' => $count,
                '--market' => $this->option('market')
            ]);

            $this->call('fake:marketing-expense', [
                '--date' => $date->format('Y-m-d H:i:s'),
                '--count' => $count,
                '--market' => $this->option('market')
            ]);

            $this->call('fake:warehouse-statistic', [
                '--date' => $date->format('Y-m-d H:i:s'),
                '--count' => $count,
                '--market' => $this->option('market')
            ]);

            $this->info('Generated ' . $date->format('Y-m-d H:i:s'));
            sleep(1);
        }

        return 0;
    }
}
