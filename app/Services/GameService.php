<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\DB as FacadesDB;
use Exception;

class GameService
{
    protected function getYear(int $year = null): int
    {
        return $year ?? (int) env('APP_ANO');
    }

    /**
     * Cria um jogo (simulado ou confirmado),
     * gerando automaticamente números únicos entre 1 e 60.
     */
    public function createGame(
    int $gameSize,
    float $totalValue,
    int $year,
    string $status = 'simulated'
    ): int
 {
        $year = $this->getYear($year);

        // Gera números únicos
        $numbers = $this->generateNumbers($gameSize);

        return DB::transaction(function () use ($gameSize, $numbers, $totalValue, $year, $status) {

            $gameId = DB::table('games')->insertGetId([
                'game_size'   => $gameSize,
                'total_value' => $totalValue,
                'status'      => $status,
                'year'        => $year,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            $rows = array_map(fn ($number) => [
                'game_id'    => $gameId,
                'number'     => $number,
                'year'       => $year,
                'created_at' => now(),
                'updated_at' => now(),
            ], $numbers);

            DB::table('game_numbers')->insert($rows);

            return $gameId;
        });
    }

    /**
     * Gera números únicos entre 1 e 60.
     */
    protected function generateNumbers(int $gameSize): array
    {
        if ($gameSize < 6 || $gameSize > 20) {
            throw new Exception('Tamanho de jogo inválido.');
        }

        $numbers = range(1, 60);
        shuffle($numbers);

        return array_slice($numbers, 0, $gameSize);
    }

    /**
     * Atualiza o status de um jogo.
     */
    public function updateStatus(int $gameId, string $status): void
    {
        if (!in_array($status, ['simulated', 'confirmed'])) {
            throw new Exception('Status inválido.');
        }

        DB::table('games')
            ->where('id', $gameId)
            ->update([
                'status' => $status,
                'updated_at' => now(),
            ]);
    }

    /**
     * Retorna jogos por ano e status.
     */
    public function getGames(int $year = null, string $status = null)
    {
        $year = $this->getYear($year);

        $query = DB::table('games')->where('year', $year);

        if ($status) {
            $query->where('status', $status);
        }

        return $query->get();
    }

    /**
     * Retorna os números de um jogo.
     */
    public function getGameNumbers(int $gameId): array
    {
        return DB::table('game_numbers')
            ->where('game_id', $gameId)
            ->orderBy('number')
            ->pluck('number')
            ->toArray();
    }

    public function addNumbersToGame(int $gameId, array $numbers, int $year): void
    {
        // 🔒 Se já existem números, NÃO faz nada
        $alreadyExists = DB::table('game_numbers')
            ->where('game_id', $gameId)
            ->exists();

        if ($alreadyExists) {
            return;
        }

        foreach ($numbers as $number) {
            DB::table('game_numbers')->insert([
                'game_id'    => $gameId,
                'number'     => $number,
                'year'       => $year,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }


    public function hasConfirmedGames(int $year): bool
    {
        return DB::table('games')
            ->where('year', $year)
            ->where('status', 'confirmed')
            ->exists();
    }


}
