<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class ParticipantService
{
    protected function getYear(int $year = null): int
    {
        return $year ?? (int) env('APP_ANO');
    }

    /** Lista todos os participantes do ano */
    public function all(int $year = null)
    {
        $year = $this->getYear($year);

        return DB::table('participants')
            ->where('year', $year)
            ->get();
    }

    /** Cria um participante */
    public function create(
        string $name,
        string $phone,
        string $hash1,
        string $hash2,
        int $year = null
    ): int {
        $year = $this->getYear($year);

        return DB::table('participants')->insertGetId([
            'name'       => $name,
            'phone'      => $phone,
            'hash1'      => $hash1,
            'hash2'      => $hash2,
            'year'       => $year,
            'created_at'=> now(),
            'updated_at'=> now(),
        ]);
    }

    /** Busca participante pelo link público */
    public function findByHashes(string $hash1, string $hash2, int $year = null)
    {
        $year = $this->getYear($year);

        return DB::table('participants')
            ->where('hash1', $hash1)
            ->where('hash2', $hash2)
            ->where('year', $year)
            ->first();
    }

    public function findOrCreateAdminParticipant(string $phone, int $year)
{
    $participant = \DB::table('participants')
        ->where('phone', $phone)
        ->where('year', $year)
        ->first();

    if ($participant) {
        return (object) $participant;
    }

    // Gera hashes conforme regra do projeto
    $hash1 = md5($year) . sha1($phone);
    $hash2 = md5($year) . sha1($phone) . sha1($year);

    $id = \DB::table('participants')->insertGetId([
        'name'       => 'Organizador',
        'phone'      => $phone,
        'hash1'      => $hash1,
        'hash2'      => $hash2,
        'year'       => $year,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return (object) [
        'id'    => $id,
        'phone' => $phone,
        'year'  => $year,
    ];
}

}
