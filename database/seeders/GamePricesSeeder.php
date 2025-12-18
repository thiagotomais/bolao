<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GamePricesSeeder extends Seeder
{
    public function run(): void
    {
        $year = env('APP_ANO', date('Y'));

        $prices = [
            [6,   6.00],
            [7,  42.00],
            [8, 168.00],
            [9, 504.00],
            [10, 1260.00],
            [11, 2772.00],
            [12, 5544.00],
            [13, 10296.00],
            [14, 18018.00],
            [15, 30030.00],
            [16, 48048.00],
            [17, 74256.00],
            [18, 111384.00],
            [19, 162792.00],
            [20, 232560.00],
        ];

        foreach ($prices as [$gameSize, $price]) {
            DB::table('game_prices')->insert([
                'game_size'  => $gameSize,
                'price'      => $price,
                'year'       => $year,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}