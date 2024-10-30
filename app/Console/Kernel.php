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
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('fetch:inventory')->everyTenMinutes();
        $schedule->command('fetch:inventory-item')->everyTenMinutes();
        $schedule->command('fetch:products')->dailyAt('00:00');
        $schedule->call(function () {
            $assets = \App\Models\MstStrokeDies::whereColumn('current_qty', '>=', \DB::raw('std_stroke * 0.8'))->get();

            foreach ($assets as $asset) {
                // Send PM reminder email
                \Mail::to('prasetyo@ptmkm.co.id')->send(new \App\Mail\PMReminderMail($asset));
            }
        })->daily();  // Set to run daily, you can adjust the frequency as needed
    }
}
