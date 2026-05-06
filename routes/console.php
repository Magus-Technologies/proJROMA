<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ── Programar jobs de SUNAT ────────────────────────────────────────────
// Schedule::command('sunat:enviar-facturas')->everyFiveMinutes();
// Schedule::command('sunat:resumen-diario')->dailyAt('23:30');
// Schedule::command('sunat:comunicacion-baja')->dailyAt('01:00');
