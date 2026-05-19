<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Alertas financeiros e de estoque — roda todo dia às 08:00
Schedule::command('invexa:check-alerts')->dailyAt('08:00');
