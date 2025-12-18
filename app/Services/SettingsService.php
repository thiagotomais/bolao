<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SettingsService
{
    protected function getYear(int $year = null): int
    {
        return $year ?? (int) env('APP_ANO', date('Y'));
    }

    public function get(string $key, int $year = null)
    {
        $year = $this->getYear($year);

        $setting = DB::table('settings')
            ->where('key', $key)
            ->where('year', $year)
            ->first();

        if (!$setting) {
            return null;
        }

        return match ($setting->type) {
            'int'      => (int) $setting->value,
            'decimal'  => (float) $setting->value,
            'bool'     => (bool) $setting->value,
            'datetime' => Carbon::parse($setting->value),
            default    => $setting->value,
        };
    }

    public function set(string $key, $value, string $type, int $year = null): void
    {
        $year = $this->getYear($year);

        DB::table('settings')->updateOrInsert(
            ['key' => $key, 'year' => $year],
            [
                'value'      => $value,
                'type'       => $type,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }

    public function all(int $year = null): array
    {
        $year = $this->getYear($year);

        return DB::table('settings')
            ->where('year', $year)
            ->get()
            ->mapWithKeys(function ($setting) {
                return [$setting->key => $this->get($setting->key, $setting->year)];
            })
            ->toArray();
    }

    // Métodos semânticos (contrato claro)

    public function getParticipationValue(int $year = null): float
    {
        return (float) $this->get('participation_value', $year);
    }

    public function getClosingDatetime(int $year = null): Carbon
    {
        return $this->get('closing_datetime', $year);
    }
}