<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SettingsSeeder extends Seeder
{
    public function run(): void
{
    $year = (int) env('APP_ANO', date('Y'));

    DB::table('settings')->updateOrInsert(
        ['key' => 'participation_value', 'year' => $year],
        [
            'value'      => '50.00',
            'type'       => 'decimal',
            'created_at' => now(),
            'updated_at' => now(),
        ]
    );

    DB::table('settings')->updateOrInsert(
        ['key' => 'closing_datetime', 'year' => $year],
        [
            'value'      => '2025-12-30 23:59:59',
            'type'       => 'datetime',
            'created_at' => now(),
            'updated_at' => now(),
        ]
    );

    DB::table('settings')->updateOrInsert(
        ['key' => 'pool_status', 'year' => $year],
        [
            'value'      => 'open',
            'type'       => 'string',
            'created_at' => now(),
            'updated_at' => now(),
        ]
    );
}

}
