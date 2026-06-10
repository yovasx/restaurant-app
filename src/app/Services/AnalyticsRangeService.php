<?php

namespace App\Services;

use Illuminate\Support\Carbon;

class AnalyticsRangeService
{
    public const VALID_RANGES = [7, 14, 30, 60, 90];

    private int $days;
    private Carbon $from;
    private Carbon $to;
    private Carbon $prevFrom;
    private Carbon $prevTo;

    public function __construct(int $days = 30, ?Carbon $to = null)
    {
        $this->days = in_array($days, self::VALID_RANGES, true) ? $days : 30;
        $this->to = ($to ?? now())->copy()->endOfDay();
        $this->from = $this->to->copy()->subDays($this->days)->startOfDay();
        $this->prevTo = $this->from->copy()->subSecond()->endOfDay();
        $this->prevFrom = $this->from->copy()->subDays($this->days)->startOfDay();
    }

    public static function fromRequest(?string $range, ?Carbon $to = null): self
    {
        return new self((int) ($range ?? 30), $to);
    }

    public function days(): int
    {
        return $this->days;
    }

    public function from(): Carbon
    {
        return $this->from;
    }

    public function to(): Carbon
    {
        return $this->to;
    }

    public function prevFrom(): Carbon
    {
        return $this->prevFrom;
    }

    public function prevTo(): Carbon
    {
        return $this->prevTo;
    }

    public function rangeInfo(): array
    {
        return [
            'days' => $this->days,
            'from' => $this->from->format('d/m/Y'),
            'to' => $this->to->format('d/m/Y'),
            'prev_from' => $this->prevFrom->format('d/m/Y'),
            'prev_to' => $this->prevTo->format('d/m/Y'),
        ];
    }

    public static function delta(int|float $current, int|float $previous): int
    {
        if ($previous > 0) {
            return (int) round((($current - $previous) / $previous) * 100);
        }
        return $current > 0 ? 100 : 0;
    }
}
