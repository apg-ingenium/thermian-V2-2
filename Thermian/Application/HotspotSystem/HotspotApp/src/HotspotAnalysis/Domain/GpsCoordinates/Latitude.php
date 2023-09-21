<?php
declare(strict_types=1);

namespace Hotspot\HotspotAnalysis\Domain\GpsCoordinates;

use InvalidArgumentException;

class Latitude
{
    private Angle $angle;

    public static function fromAngularPosition(int $degrees, int $minutes, float $seconds, string $direction): self
    {
        $direction = strtolower($direction);

        $sign = match ($direction) {
            'n', 'north' => 1,
            's', 'south' => - 1,
            default => throw new InvalidArgumentException("Invalid latitude direction: {$direction}"),
        };

        return new Latitude(Angle::fromParts($sign * $degrees, $sign * $minutes, $sign * $seconds));
    }

    public static function fromDecimalDegrees(float $value): self
    {
        return new Latitude(Angle::fromDecimalDegrees($value));
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
        return $this->angle->degrees();
    }

    public function minutes(): int
    {
        return $this->angle->minutes();
    }

    public function seconds(): float
    {
        return $this->angle->seconds();
    }

    public function direction(): string
    {
        return $this->angle->decimalDegrees() > 0 ? 'N' : 'S';
    }

    public function equals(Latitude $other): bool
    {
        return $this->angle->equals($other->angle);
    }
}
