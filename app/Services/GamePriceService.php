<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class GamePriceService
{
    protected function getYear(int $year = null): int
    {
        return $year ?? (int) env('APP_ANO');
    }

    public function getPrice(int $gameSize, int $year = null): ?float
    {
        $year = $this->getYear($year);

        $row = DB::table('game_prices')
            ->where('game_size', $gameSize)
            ->where('year', $year)
            ->first();

        return $row ? (float) $row->price : null;
    }

    /**
     * Retorna a tabela completa de preços,
     * ordenada do maior game_size para o menor.
     */
    public function allPrices(int $year = null): array
    {
        $year = $this->getYear($year);

        return DB::table('game_prices')
            ->where('year', $year)
            ->orderByDesc('game_size')
            ->pluck('price', 'game_size')
            ->map(fn ($price) => (float) $price)
            ->toArray();
    }
}
