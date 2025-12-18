<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class ProbabilityService
{
    protected function getYear(int $year = null): int
    {
        return $year ?? (int) env('APP_ANO');
    }

    /**
     * Retorna as odds oficiais para um game_size.
     */
    public function getOdds(int $gameSize, int $year = null): ?array
    {
        $year = $this->getYear($year);

        $row = DB::table('probability_tables')
            ->where('game_size', $gameSize)
            ->where('year', $year)
            ->first();

        if (!$row) {
            return null;
        }

        return [
            'sena'   => (int) $row->sena_odds,
            'quina'  => (int) $row->quina_odds,
            'quadra' => (int) $row->quadra_odds,
        ];
    }

    /**
     * Retorna todas as odds do ano,
     * ordenadas do maior game_size para o menor.
     */
    public function allOdds(int $year = null): array
    {
        $year = $this->getYear($year);

        return DB::table('probability_tables')
            ->where('year', $year)
            ->orderByDesc('game_size')
            ->get()
            ->mapWithKeys(fn ($row) => [
                $row->game_size => [
                    'sena'   => (int) $row->sena_odds,
                    'quina'  => (int) $row->quina_odds,
                    'quadra' => (int) $row->quadra_odds,
                ]
            ])
            ->toArray();
    }

    /**
     * Retorna as odds formatadas para exibição.
     * Ex: "1 em 50.063.860"
     */
    public function formatOdds(int $odds): string
    {
        return '1 em ' . number_format($odds, 0, ',', '.');
    }
}