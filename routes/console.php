<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use App\Models\CafeTable;
use Illuminate\Support\Facades\Schedule;

Schedule::call(function () {
    CafeTable::where('status', 'occupied')->update(['status' => 'available']);
})->dailyAt('06:00');

