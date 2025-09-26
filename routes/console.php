<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\ProcessRecurrences;
use App\Jobs\CloseInvoices;
use App\Jobs\GoalAlerts;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::job(new ProcessRecurrences)->everyFifteenMinutes();
Schedule::job(new CloseInvoices(app(App\Services\CardService::class)))->daily();
Schedule::job(new GoalAlerts)->daily();
