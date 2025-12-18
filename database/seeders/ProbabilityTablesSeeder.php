<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProbabilityTablesSeeder extends Seeder
{
    public function run(): void
    {
        $year = env('APP_ANO', date('Y'));

        $probabilities = [
            [6,  50063860, 154518, 2332],
            [7,   7151980,  44981, 1038],
            [8,   1787995,  17192,  539],
            [9,    595998,   7791,  312],
            [10,   238399,   3973,  195],
            [11,   108363,   2211,  129],
            [12,    54182,   1317,   90],
            [13,    29175,    828,   65],
            [14,    16671,    544,   48],
            [15,    10003,    370,   37],
            [16,     6252,    260,   29],
            [17,     4045,    188,   23],
            [18,     2697,    139,   19],
            [19,     1845,    105,   16],
            [20,     1292,     81,   13],
        ];

        foreach ($probabilities as [$gameSize, $sena, $quina, $quadra]) {
            DB::table('probability_tables')->insert([
                'game_size'    => $gameSize,
                'sena_odds'    => $sena,
                'quina_odds'   => $quina,
                'quadra_odds'  => $quadra,
                'year'         => $year,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }
    }
}
