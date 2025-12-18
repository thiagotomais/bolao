<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;

class ParticipationService
{
    protected SettingsService $settingsService;

    public function __construct(SettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
    }

    protected function getYear(int $year = null): int
    {
        return $year ?? (int) env('APP_ANO');
    }

    public function addParticipation(
        int $participantId,
        int $quantity,
        int $year = null,
        bool $isAdmin = false
    ): void {
        $year = $this->getYear($year);

        $unitValue  = $this->settingsService->getParticipationValue($year);
        $totalValue = bcmul($unitValue, $quantity, 2);

        DB::table('participations')->insert([
            'participant_id' => $participantId,
            'quantity'       => $quantity,
            'unit_value'     => $unitValue,
            'total_value'    => $totalValue,
            'is_admin'       => $isAdmin,
            'year'           => $year,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);
    }

    /** 🔥 Fonte da verdade financeira */
    public function getTotalCollectedValue(int $year = null): float
    {
        $year = $this->getYear($year);

        return (float) DB::table('participations')
            ->where('year', $year)
            ->sum('total_value');
    }

    public function getParticipantCollectedValue(int $participantId, int $year = null): float
    {
        $year = $this->getYear($year);

        return (float) DB::table('participations')
            ->where('participant_id', $participantId)
            ->where('year', $year)
            ->sum('total_value');
    }

     /** Percentual correto (baseado em valor) */
    public function calculateParticipationPercent(int $participantId, int $year = null): float
    {
        $year  = $this->getYear($year);
        $total = $this->getTotalCollectedValue($year);

        if ($total == 0.0) {
            return 0.0;
        }

        $userTotal = $this->getParticipantCollectedValue($participantId, $year);

        return round($userTotal / $total, 8);
    }

    /** Fração extra do admin (baseada em valor) */
    public function getAdminFraction(int $year = null): float
    {
        $year = $this->getYear($year);

        $adminTotal = (float) DB::table('participations')
            ->where('is_admin', true)
            ->where('year', $year)
            ->sum('total_value');

        $total = $this->getTotalCollectedValue($year);

        if ($total == 0.0) {
            return 0.0;
        }

        return round($adminTotal / $total, 8);
    }

    public function getParticipations(int $participantId, int $year = null)
    {
        $year = $this->getYear($year);

        return DB::table('participations')
            ->where('participant_id', $participantId)
            ->where('year', $year)
            ->get();
    }

    public function addAdminComplement(
    int $participantId,
    float $totalValue,
    int $year
): void {
    DB::table('participations')->insert([
        'participant_id' => $participantId,
        'quantity'       => 0,
        'unit_value'     => 0,
        'total_value'    => $totalValue,
        'is_admin'       => true,
        'year'           => $year,
        'created_at'     => now(),
        'updated_at'     => now(),
    ]);
}

}