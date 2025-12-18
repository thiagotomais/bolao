<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Exception;

class ReceiptService
{
    protected function getYear(int $year = null): int
    {
        return $year ?? (int) env('APP_ANO');
    }

    /**
     * Faz upload do comprovante e associa a um jogo confirmado.
     */
    public function uploadForGame(
        UploadedFile $file,
        int $gameId,
        int $year = null
    ): int {
        $year = $this->getYear($year);

        // Validação básica
        if (!in_array($file->getClientOriginalExtension(), ['pdf', 'jpg', 'jpeg', 'png'])) {
            throw new Exception('Tipo de arquivo não permitido.');
        }

        // Verifica se o jogo existe e está confirmado
        $game = DB::table('games')
            ->where('id', $gameId)
            ->where('year', $year)
            ->where('status', 'confirmed')
            ->first();

        if (!$game) {
            throw new Exception('Jogo inválido ou não confirmado.');
        }

        $path = $file->store(
            "receipts/{$year}",
            'public'
        );

        return DB::table('receipts')->insertGetId([
            'game_id'    => $gameId,
            'file_path' => $path,
            'file_type' => $file->getClientOriginalExtension(),
            'year'      => $year,
            'created_at'=> now(),
            'updated_at'=> now(),
        ]);
    }

    /**
     * Lista comprovantes por ano.
     */
    public function getReceipts(int $year = null)
    {
        $year = $this->getYear($year);

        return DB::table('receipts')
            ->where('year', $year)
            ->get();
    }

    /**
     * Retorna o comprovante de um jogo específico.
     */
    public function getReceiptByGame(int $gameId, int $year = null)
    {
        $year = $this->getYear($year);

        return DB::table('receipts')
            ->where('game_id', $gameId)
            ->where('year', $year)
            ->first();
    }
}
