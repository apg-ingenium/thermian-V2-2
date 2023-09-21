<?php
declare(strict_types=1);

namespace Hotspot\HotspotAnalysis\Domain\GpsCoordinates;

class Angle
{
    private float $value;

    /** @var array<string, mixed> */
    private array $dms;

    public static function fromParts(int $degrees, int $minutes, float $seconds): self
    {
        return new Angle($degrees + ($minutes / 60) + ($seconds / 3600));
    }

    public static function fromDecimalDegrees(float $value): self
    {
        return new Angle($value);
    }

    private function __construct(float $value)
    {
        $this->value = $value;

        $sign = $value > 0 ? 1 : -1;
        $value = abs($value);

        $wholeDecrees = (int)floor($value);
        $minutes = ($value - $wholeDecrees) * 60;
        $wholeMinutes = (int)floor($minutes);
        $seconds = ($minutes - $wholeMinutes) * 60;

        $this->dms = [
            'degrees' => $sign * $wholeDecrees,
            'minutes' => $sign * $wholeMinutes,
            'seconds' => $sign * $seconds,
        ];
    }

    /** @return array<string, mixed> */
    public function degreesMinutesAndSeconds(): array
    {
        return $this->dms;
    }

    public function decimalDegrees(): float
    {
        return $this->value;
    }

    public function degrees(): int
    {
        return $this->dms['degrees'];
    }

    public function minutes(): int
    {
        return $this->dms['minutes'];
    }

    public function seconds(): float
    {
        return $this->dms['seconds'];
    }

    public function equals(Angle $other): bool
    {
        $thisAngle = (($this->decimalDegrees() % 360) + 360) % 360;
        $otherAngle = (($other->decimalDegrees() % 360) + 360) % 360;

        return $thisAngle === $otherAngle;
    }
}
