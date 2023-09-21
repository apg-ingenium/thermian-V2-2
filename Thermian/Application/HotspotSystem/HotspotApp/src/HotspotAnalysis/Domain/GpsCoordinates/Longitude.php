<?php
declare(strict_types=1);

namespace Hotspot\HotspotAnalysis\Domain\GpsCoordinates;

use InvalidArgumentException;

class Longitude
{
    private Angle $angle;

    public static function fromAngularPosition(int $degrees, int $minutes, float $seconds, string $direction): self
    {
        $direction = strtolower($direction);

        $sign = match ($direction) {
            'e', 'east' => 1,
            'w', 'west' => - 1,
            default => throw new InvalidArgumentException("Invalid longitude direction: {$direction}"),
        };

        return new Longitude(Angle::fromParts($sign * $degrees, $sign * $minutes, $sign * $seconds));
    }

    public static function fromDecimalDegrees(float $value): self
    {
        return new Longitude(Angle::fromDecimalDegrees($value));
    }

    private function __construct(Angle $angle)
    {
        $this->angle = $angle;
    }

    public function decimalDegrees(): float
    {
        return $this->angle->decimalDegrees();
    }

    /** @return array<mixed> */
    public function degreesMinutesSecondsAndDirection(): array
    {
        return [
            ...array_map(fn($value) => abs($value), $this->angle->degreesMinutesAndSeconds()),
            'direction' => $this->direction(),
        ];
    }

    public function degrees(): int
    {
        return abs($this->angle->degrees());
    }

    public function minutes(): int
    {
        return abs($this->angle->minutes());
    }

    public function seconds(): float
    {
        return abs($this->angle->seconds());
    }

    public function direction(): string
    {
        return $this->angle->degrees() > 0 ? 'E' : 'W';
    }

    public function equals(Longitude $other): bool
    {
        return $this->angle->equals($other->angle);
    }
}
