<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{

    protected $commands = [
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->call('App\Http\Controllers\EventController@checkListNotification')->weekdays()
            ->everyFiveMinutes()
            ->timezone('America/Mexico_City')
            ->between('8:00','18:00');
    }
}
