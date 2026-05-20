<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ── Agendamentos Invexa
Schedule::command('invexa:expire-trials')->dailyAt('01:00');
Schedule::command('invexa:sync-subscriptions')->dailyAt('02:00');
Schedule::command('invexa:daily-alerts')->dailyAt('08:00');
Schedule::command('invexa:trial-ending-emails')->dailyAt('09:00');
