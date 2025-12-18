<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\ParticipationService;
use App\Services\GameService;
use App\Services\ProbabilityService;

class ParticipantController extends Controller
{
    protected ParticipationService $participationService;
    protected GameService $gameService;
    protected ProbabilityService $probabilityService;

    public function __construct(
        ParticipationService $participationService,
        GameService $gameService,
        ProbabilityService $probabilityService
    ) {
        $this->participationService = $participationService;
        $this->gameService = $gameService;
        $this->probabilityService = $probabilityService;
    }

    public function show(string $hash1, string $hash2)
    {
        $year = (int) env('APP_ANO');

        $participant = DB::table('participants')
            ->where('hash1', $hash1)
            ->where('hash2', $hash2)
            ->where('year', $year)
            ->first();

        if (!$participant) {
            abort(404);
        }

        $percent = $this->participationService
            ->calculateParticipationPercent($participant->id, $year);

        $participantValue = $this->participationService
            ->getParticipantCollectedValue($participant->id, $year);

        $totalValue = $this->participationService
            ->getTotalCollectedValue($year);

        $games = $this->gameService->getGames($year, 'confirmed');

        foreach ($games as $game) {
            $game->numbers = $this->gameService->getGameNumbers($game->id);
        }

        $probabilities = $this->probabilityService->allOdds($year);

        $estimatedPrize = app(\App\Services\SettingsService::class)
        ->getEstimatedPrize($year);

        $estimatedUserPrize = $estimatedPrize * $percent;

        $drawNumbers = app(\App\Services\SettingsService::class)
        ->getDrawNumbers($year);


        return view('participant.show', compact(
            'participant',
            'percent',
            'participantValue',
            'totalValue',
            'games',
            'probabilities',
            'estimatedPrize',
            'estimatedUserPrize',
            'drawNumbers'
        ));
    }
}
