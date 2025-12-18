<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AdminService;
use App\Services\ParticipantService;
use App\Services\ParticipationService;
use App\Services\GameService;
use App\Services\GameStrategyService;
use App\Services\ReceiptService;
use App\Services\SettingsService;


class AdminController extends Controller
{
    public function __construct(
        protected AdminService $adminService,
        protected ParticipantService $participantService,
        protected ParticipationService $participationService,
        protected GameService $gameService,
        protected GameStrategyService $gameStrategyService,
        protected ReceiptService $receiptService,
        protected SettingsService $settingsService
    ) {}


    /** Adiciona participação */
    public function addParticipation(Request $request, int $participantId)
    {
        $data = $request->validate([
            'quantity' => 'required|integer|min:1',
            'is_admin' => 'sometimes|boolean',
        ]);

        $this->participationService->addParticipation(
            $participantId,
            $data['quantity'],
            null,
            $data['is_admin'] ?? false
        );

        return back()->with('success', 'Participação adicionada.');
    }

    /** Simula jogos */
   public function simulateGames()
    {
        $year = (int) env('APP_ANO');

        $totalValue = $this->participationService->getTotalCollectedValue($year);

        $simulation = $this->gameStrategyService->simulate($totalValue, $year);

        $summary = $this->gameStrategyService
        ->getSimulationSummary($totalValue, $simulation, $year);


        return view('admin.simulate', [
            'totalValue' => $totalValue,
            'simulation' => $simulation,
            'summary' => $summary
        ]);
    }

    /** Fecha bolão */
    public function closePool()
    {
        $this->adminService->closePool();

        return back()->with('success', 'Bolão fechado.');
    }

    /** Lista jogos */
    public function games()
    {
        return view('admin.games', [
            'games' => $this->gameService->getGames(),
        ]);
    }

    /** Confirma um jogo específico */
    public function confirmGame(int $gameId)
    {
        $this->gameService->updateStatus($gameId, 'confirmed');

        return back()->with('success', 'Jogo confirmado.');
    }

    /** Upload de comprovante */
    public function uploadReceipt(Request $request, int $gameId)
    {
        $request->validate([
            'file' => 'required|file',
        ]);

        $this->receiptService->uploadForGame(
            $request->file('file'),
            $gameId
        );

        return back()->with('success', 'Comprovante anexado.');
    }

    // Listar participantes + totais
public function participants()
{
    $year = (int) env('APP_ANO');

    return view('admin.participants', [
        'participants' => $this->participantService->all($year),
        'total' => $this->participationService->getTotalCollectedValue($year),
    ]);
}

// Criar participante
public function storeParticipant(Request $request)
{
    $request->validate([
        'name'  => 'required|string',
        'phone' => 'required|string',
    ]);

    $year = (int) env('APP_ANO');

    $hash1 = md5($year) . sha1($request->phone);
    $hash2 = md5($hash1) . sha1($request->phone) . sha1($year);

    $this->participantService->create(
        $request->name,
        $request->phone,
        $hash1,
        $hash2,
        $year
    );

    return back()->with('success', 'Participante criado.');
}

// Adicionar participação
public function storeParticipation(Request $request, int $participantId)
{
    $request->validate([
        'quantity' => 'required|integer|min:1',
    ]);

    $this->participationService->addParticipation(
        $participantId,
        $request->quantity,
        null,
        $request->boolean('is_admin')
    );

    return back()->with('success', 'Participação adicionada.');
}

public function complementAndRegenerate()
{
    $year = (int) env('APP_ANO');

    // Valor da participação
    $participationValue = $this->settingsService->getParticipationValue($year);

    // Total atual
    $totalCollected = $this->participationService->getTotalCollectedValue($year);

    // Simulação atual
    $simulation = $this->gameStrategyService->simulate($totalCollected, $year);

    $summary = $this->gameStrategyService
        ->getSimulationSummary($totalCollected, $simulation, $year);

    if (!$summary['missing_for_next_game']) {
        return redirect()->route('admin.simulate');
    }

    // Complemento necessário
    $complementValue = $summary['missing_for_next_game'];

    // Participante organizador
    $adminPhone = env('ADMIN_PHONE');
    $adminParticipant = $this->participantService
        ->findOrCreateAdminParticipant($adminPhone, $year);

    // Registra participação fracionada
    $this->participationService->addAdminComplement(
        participantId: $adminParticipant->id,
        totalValue: $complementValue,
        year: $year
    );

    /*
     |--------------------------------------------------------------------------
     | A PARTIR DAQUI É A DIFERENÇA
     |--------------------------------------------------------------------------
     */

    // Recalcula total já com complemento
    $newTotal = $this->participationService->getTotalCollectedValue($year);

    // Nova simulação
    $finalSimulation = $this->gameStrategyService->simulate($newTotal, $year);

    // Geração REAL dos jogos
    foreach ($finalSimulation as $item) {
        for ($i = 0; $i < $item['quantity']; $i++) {
            $numbers = $this->generateRandomNumbers($item['game_size']);

            $gameId = $this->gameService->createGame(
            $item['game_size'],
            $item['unit_price'],
            $year,
            'confirmed'
        );

        // depois grava os números
        $this->gameService->addNumbersToGame($gameId, $numbers);


        }
    }

    // Fecha o bolão
    $this->settingsService->set('pool_status', 'closed', $year);

    return redirect()
        ->route('admin.games')
        ->with('success', 'Complemento realizado e jogos gerados com sucesso.');
    }




    
  public function generateGames(Request $request)
    {
        $year = (int) env('APP_ANO');

        // complemento vem da intenção do botão
        $allowComplement = (bool) $request->input('allow_complement', false);

        $this->adminService->finalizePool($year, $allowComplement);

        return redirect()
            ->route('admin.games')
            ->with('success', 'Jogos gerados com sucesso.');
    }



public function finalizePool(int $year, bool $allowComplement = false): void
{
    // Proteção: já gerado
    if ($this->gameService->hasConfirmedGames($year)) {
        return;
    }

    $total = $this->participationService->getTotalCollectedValue($year);

    $simulation = $this->gameStrategyService->simulate($total, $year);
    $summary = $this->gameStrategyService->getSimulationSummary($total, $simulation, $year);

    // Complemento opcional
    if ($allowComplement && $summary['missing_for_next_game']) {
        $admin = $this->participantService->findOrCreateAdminParticipant(
            env('ADMIN_PHONE'),
            $year
        );

        $this->participationService->addAdminComplement(
            $admin->id,
            $summary['missing_for_next_game'],
            $year
        );

        // recalcula após complemento
        $total = $this->participationService->getTotalCollectedValue($year);
        $simulation = $this->gameStrategyService->simulate($total, $year);
    }

    // Geração definitiva
    foreach ($simulation as $item) {
        for ($i = 0; $i < $item['quantity']; $i++) {
            $numbers = $this->generateNumbers($item['game_size']);

            $gameId = $this->gameService->createGame(
                $item['game_size'],
                $item['unit_price'],
                $year,
                'confirmed'
            );

            $this->gameService->addNumbersToGame($gameId, $numbers, $year);
        }
    }

    $this->settingsService->set('pool_status', 'closed', $year);
}



}
            