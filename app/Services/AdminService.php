<?php

namespace App\Services;

use App\Services\SettingsService;
use App\Services\ParticipationService;
use App\Services\GameService;
use App\Services\GameStrategyService;
use App\Services\ReceiptService;
use App\Services\ProbabilityService;
use App\Services\ParticipantService;

use Illuminate\Support\Facades\DB;

class AdminService
{
    public function __construct(
        protected SettingsService $settingsService,
        protected ParticipationService $participationService,
        protected GameService $gameService,
        protected GameStrategyService $gameStrategyService,
        protected ReceiptService $receiptService,
        protected ProbabilityService $probabilityService,
        protected ParticipantService $participantService        
    ) {}

    /**
     * Fecha o bolão:
     * - marca status como fechado
     * - confirma jogos simulados
     */
    public function closePool(int $year = null): void
    {
        $year = $year ?? (int) env('APP_ANO');

        $this->settingsService->set('pool_status', 'closed', 'string', $year);

        // confirma jogos simulados
        foreach ($this->gameService->getGames($year, 'simulated') as $game) {
            $this->gameService->updateStatus($game->id, 'confirmed');
        }
    }

    /**
     * Simula rateio proporcional (SEM persistir).
     */
    public function simulatePayouts(float $estimatedPrize, int $year = null): array
    {
        $year = $year ?? (int) env('APP_ANO');

        $totalCollected = $this->participationService->getTotalCollectedValue($year);

        if ($totalCollected == 0.0) {
            return [];
        }

        $participants = DB::table('participants')->where('year', $year)->get();

        $result = [];

        foreach ($participants as $participant) {
            $percent = $this->participationService
                ->calculateParticipationPercent($participant->id, $year);

            $result[] = [
                'participant_id' => $participant->id,
                'name'           => $participant->name,
                'percent'        => $percent,
                'estimated_win'  => round($estimatedPrize * $percent, 2),
            ];
        }

        return $result;
    }

    /**
     * Relatório consolidado (somente leitura).
     */
    public function getFinalReport(int $year = null): array
    {
        $year = $year ?? (int) env('APP_ANO');

        return [
            'total_collected' => $this->participationService->getTotalCollectedValue($year),
            'games'           => $this->gameService->getGames($year),
            'receipts'        => $this->receiptService->getReceipts($year),
        ];
    }

    public function finalizePool(int $year, bool $allowComplement = false): void
    {
        

    // 🔒 Proteção: não gerar jogos duas vezes
    if ($this->gameService->hasConfirmedGames($year)) {
        return;
    }

    // 💰 Total arrecadado
    $totalCollected = $this->participationService->getTotalCollectedValue($year);

    // 🧮 Simulação inicial
    $simulation = $this->gameStrategyService->simulate($totalCollected, $year);
    $summary = $this->gameStrategyService
        ->getSimulationSummary($totalCollected, $simulation, $year);

    // ➕ Complemento opcional
    if ($allowComplement && $summary['missing_for_next_game']) {

        $adminPhone = env('ADMIN_PHONE');

        $adminParticipant = $this->participantService
            ->findOrCreateAdminParticipant($adminPhone, $year);

        $this->participationService->addAdminComplement(
            $adminParticipant->id,
            $summary['missing_for_next_game'],
            $year
        );

        // 🔄 Recalcula após complemento
        $totalCollected = $this->participationService->getTotalCollectedValue($year);
        $simulation = $this->gameStrategyService->simulate($totalCollected, $year);
    }

    // 🎲 Geração definitiva dos jogos
    foreach ($simulation as $item) {
        for ($i = 0; $i < $item['quantity']; $i++) {

            $numbers = $this->generateRandomNumbers($item['game_size']);
            
            $gameId = $this->gameService->createGame(
                $item['game_size'],
                $item['unit_price'],
                $year,
                'confirmed'
            );

            $this->gameService->addNumbersToGame($gameId, $numbers, $year);
        }
    }

    // 🔐 Fecha o bolão
    $this->settingsService->set('pool_status', 'closed', $year);
}


private function generateRandomNumbers(int $gameSize): array
{
    $numbers = [];

    while (count($numbers) < $gameSize) {
        $n = random_int(1, 60);
        $numbers[$n] = $n; // evita duplicados
    }

    sort($numbers);

    return array_values($numbers);
}


}
