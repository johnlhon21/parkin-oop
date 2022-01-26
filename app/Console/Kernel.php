<?php

namespace App\Console;

use AdamTyn\Lumen\Artisan\StorageLinkCommand;
use App\Console\Commands\ExampleCommand;
use App\Console\Commands\ScheduleMessagingCommand;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        ExampleCommand::class,
        StorageLinkCommand::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //$schedule->command('message:schedule')->everyMinute()->withoutOverlapping();
    }
}
