<?php

namespace App\Services;

use Illuminate\Support\Carbon;

class TimeSeriesNormalizer
{
    public static function fillDateGaps(array $rows, Carbon $from, Carbon $to, string $dateField = 'fecha', string $valueField = 'total'): array
    {
        $indexed = [];
        foreach ($rows as $r) {
            $date = is_object($r) ? ($r->$dateField ?? null) : ($r[$dateField] ?? null);
            if ($date) {
                $indexed[$date] = (int) (is_object($r) ? ($r->$valueField ?? 0) : ($r[$valueField] ?? 0));
            }
        }

        $filled = [];
        $current = $from->copy();
        while ($current->lte($to)) {
            $key = $current->toDateString();
            $filled[] = [
                'fecha' => $key,
                'total' => $indexed[$key] ?? 0,
            ];
            $current->addDay();
        }

        return $filled;
    }

    public static function extractValues(array $rows, string $field = 'total'): array
    {
        return array_map(fn($r) => (int) (is_object($r) ? ($r->$field ?? 0) : ($r[$field] ?? 0)), $rows);
    }

    public static function extractDates(array $rows, string $field = 'fecha', string $format = 'd/m'): array
    {
        return array_map(function($r) use ($field, $format) {
            $date = is_object($r) ? ($r->$field ?? '') : ($r[$field] ?? '');
            return $date ? Carbon::parse($date)->format($format) : '';
        }, $rows);
    }

    public static function lastValues(array $rows, int $n = 7, string $field = 'total'): array
    {
        return array_values(
            array_map(fn($r) => (int) (is_object($r) ? ($r->$field ?? 0) : ($r[$field] ?? 0)), array_slice($rows, -$n))
        );
    }
}
