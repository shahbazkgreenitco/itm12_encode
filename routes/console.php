<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;


    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */

    Artisan::command('inspire', function () {
        $this->comment(Inspiring::quote());
    })->purpose('Display an inspiring quote');

    if (config("services.azure_multi.tenant") == "") {
        Schedule::command('ms_user_sync:azure')->dailyAt('00:30')->timezone(config('app.timezone'))->withoutOverlapping();
    } else {
        Schedule::command('ms_user_sync_multiple:azure')->dailyAt('00:30')->timezone(config('app.timezone'))->withoutOverlapping();
    }

    if (config("services.service_ticket.enabled")) {
        Schedule::command('tickets:process-scheduler')->everyMinute();
    }
        
