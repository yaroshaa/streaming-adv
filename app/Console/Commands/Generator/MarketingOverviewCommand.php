<?php

namespace App\Console\Commands\Generator;

use Exception;
use Illuminate\Console\Command;

/**
 * Class MarketingOverviewCommand
 * @package App\Console\Commands\Generator
 */
class MarketingOverviewCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generator:marketing {--U|users} {--C|cart} {--W|warehouse} {--E|expense} {--I|infinity}
    {--count=?} {--sleep=?}  {--start-date=?} {--end-date=?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate marketing overview data';

    /**
     * @return int
     * @throws Exception
     */
    public function handle()
    {
        $sleep = (int) $this->option('sleep');
        $users = $this->option('users');
        $cart = $this->option('cart');
        $warehouse = $this->option('warehouse');
        $expense = $this->option('expense');
        $infinity = $this->option('infinity');
        $count = $this->option('count');
        $count = $count !== '?' ? $count : 1;
        $waitInSeconds = $sleep !== '?' && $sleep > 0 ? $sleep : 1;
        $startDate = $this->option('start-date');
        $endDate = $this->option('end-date');

        $interval = \DateInterval::createFromDateString('1 day');
        if ($startDate !== '?' && $endDate !== '?') {

            $startDate = new \DateTime($startDate);
            $endDate = new \DateTime($endDate);
            $endDate->add(new \DateInterval('P1D'));
        } else {
            $startDate = new \DateTime();
            $startDate->setTime(0, 0, 0);
            $endDate = clone $startDate;
            $endDate->add(new \DateInterval('P1D'));
        }
        $period = new \DatePeriod($startDate, $interval, $endDate);

        do {
            foreach ($period as $date) {
                $this->info('Date ' . $date->format('Y-m-d'));
                if ($expense) {
                    for ($i = 0; $i < $count; $i++) {
                        $this->info('Generate marketing expense...');
                        $statisticDate = clone $date;
                        $this->call('fake:marketing-expense', [
                            '--count' => 1,
                            '--date' => $statisticDate->format('Y-m-d H:i:s'),
                        ]);
                    }
                }

                if ($users) {
                    for ($i = 0; $i < $count; $i++) {
                        $this->info('Generate active user...');
                        $this->call('fake:active-user', [
                            '--count' => 1,
                            '--date' => $date->format('Y-m-d H:i:s'),
                        ]);
                    }
                }

                if ($cart) {
                    for ($i = 0; $i < $count; $i++) {
                        $this->info('Generate cart action...');
                        $this->call('fake:cart-action', [
                            '--count' => 1,
                            '--date' => $date->format('Y-m-d H:i:s'),
                        ]);
                    }
                }

                if ($warehouse) {
                    for ($i = 0; $i < $count; $i++) {
                        $this->info('Generate item of warehouse statistic...');
                        $statisticDate = clone $date;
                        $this->call('fake:warehouse-statistic', [
                            '--count' => 1,
                            '--date' => $statisticDate->setTime(rand(0, 23), rand(0, 59))->format('Y-m-d H:i:s'),
                        ]);
                    }
                }
            }
            $this->info('wait ' . $waitInSeconds . ' seconds...');
            sleep($waitInSeconds);
        } while($infinity);

        return 0;
    }
}
