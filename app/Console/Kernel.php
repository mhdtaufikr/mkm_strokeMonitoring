<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected $commands = [
        Commands\FetchInventoryData::class,
        Commands\FetchInventoryItemData::class,
        Commands\FetchProducts::class,
        Commands\SendPMReminder::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('fetch:inventory')
         ->everyTenMinutes()
         ->withoutOverlapping()
         ->timeout(120); // 2 minutes

        $schedule->command('fetch:inventory-item')
                ->everyTenMinutes()
                ->withoutOverlapping()
                ->timeout(120); // 2 minutes

        $schedule->command('fetch:products')
                ->dailyAt('00:00')
                ->timeout(300); // 5 minutes

        $schedule->command('pm:send-reminder')
                ->daily()
                ->withoutOverlapping()
                ->timeout(180); // 3 minutes

    }
}
