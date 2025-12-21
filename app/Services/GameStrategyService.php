<?php

namespace App\Services;

class GameStrategyService
{
    protected GamePriceService $gamePriceService;

    public function __construct(GamePriceService $gamePriceService)
    {
        $this->gamePriceService = $gamePriceService;
    }

    protected function getYear(int $year = null): int
    {
        return $year ?? (int) env('APP_ANO');
    }

    /**
     * Estratégia: repetição inteligente com fallback
     *
     * - Sempre tenta o maior game_size possível
     * - Repete enquanto houver saldo
     * - Só então passa para o próximo menor
     * - Nunca arredonda valores
     */
    public function simulate(float $totalValue, int $year = null): array
{
    $year = $year ?? (int) env('APP_ANO');

    // preços ordenados do MAIOR para o MENOR
    $prices = $this->gamePriceService->allPrices($year);
    krsort($prices);

    $result = [];
    $remaining = $totalValue;

    foreach ($prices as $gameSize => $unitPrice) {
        if ($unitPrice <= 0) {
            continue;
        }

        $quantity = (int) floor($remaining / $unitPrice);

        if ($quantity > 0) {
            $total = $quantity * $unitPrice;

            $result[] = [
                'game_size'   => $gameSize,
                'quantity'    => $quantity,
                'unit_price'  => $unitPrice,
                'total_price' => $total,
            ];

            $remaining -= $total;
        }
    }

    return $result;
}

public function getSimulationSummary(float $totalValue, array $simulation, int $year): array
{
    $used = array_sum(array_column($simulation, 'total_price'));
    $remaining = round($totalValue - $used, 2);

    $prices = $this->gamePriceService->allPrices($year);
    $minPrice = min($prices);

    $missing = null;

    if ($remaining > 0 && $remaining < $minPrice) {
        $missing = round($minPrice - $remaining, 2);
    }

    return [
        'used' => $used,
        'remaining' => $remaining,
        'min_game_price' => $minPrice,
        'missing_for_next_game' => $missing,
    ];
}

public function simulateProgress(float $totalValue, int $year = null): array
{
    $year = $year ?? (int) env('APP_ANO');

    $prices = $this->gamePriceService->allPrices($year); // [game_size => price]
    ksort($prices); // garante ordem crescente

    $current = null;
    $next = null;

    foreach ($prices as $size => $price) {
        if ($price <= $totalValue) {
            $current = [
                'game_size' => $size,
                'price' => $price
            ];
        } elseif (!$next) {
            $next = [
                'game_size' => $size,
                'price' => $price
            ];
            break;
        }
    }

    if (!$current) {
        return [
            'status' => 'insufficient',
            'needed_for_first' => reset($prices),
        ];
    }

    if (!$next) {
        return [
            'status' => 'max_reached',
            'current' => $current
        ];
    }

    $missing = $next['price'] - $totalValue;
    $progress = ($totalValue / $next['price']) * 100;

    return [
        'status' => 'progress',
        'current' => $current,
        'next' => $next,
        'missing' => round($missing, 2),
        'progress' => round($progress, 2),
    ];
}


}
