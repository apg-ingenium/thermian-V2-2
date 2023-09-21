<?php
declare(strict_types=1);

namespace Hotspot\HotspotAnalysis\Domain\GpsCoordinates;

use RuntimeException;

class GpsCoordinatesBuilder
{
    private ?int $latitudeDegrees;
    private ?int $latitudeMinutes;
    private ?float $latitudeSeconds;
    private ?string $latitudeDirection;

    private ?int $longitudeDegrees;
    private ?int $longitudeMinutes;
    private ?float $longitudeSeconds;
    private ?string $longitudeDirection;

    public static function gpsCoordinates(): self
    {
        return new GpsCoordinatesBuilder();
    }

    public function __construct()
    {
        $this->latitudeDegrees = null;
        $this->latitudeMinutes = null;
        $this->latitudeSeconds = null;
        $this->latitudeDirection = null;

        $this->longitudeDegrees = null;
        $this->longitudeMinutes = null;
        $this->longitudeSeconds = null;
        $this->longitudeDirection = null;
    }

    public function withLatitudeDegrees(int $latitudeDegrees): self
    {
        $this->latitudeDegrees = $latitudeDegrees;

        return $this;
    }

    public function withLatitudeMinutes(int $latitudeMinutes): self
    {
        $this->latitudeMinutes = $latitudeMinutes;

        return $this;
    }

    public function withLatitudeSeconds(float $latitudeSeconds): self
    {
        $this->latitudeSeconds = $latitudeSeconds;

        return $this;
    }

    public function withLatitudeDirection(string $latitudeDirection): self
    {
        $this->latitudeDirection = $latitudeDirection;

        return $this;
    }

    public function withLongitudeDegrees(int $longitudeDegrees): self
    {
        $this->longitudeDegrees = $longitudeDegrees;

        return $this;
    }

    public function withLongitudeMinutes(int $longitudeMinutes): self
    {
        $this->longitudeMinutes = $longitudeMinutes;

        return $this;
    }

    public function withLongitudeSeconds(float $longitudeSeconds): self
    {
        $this->longitudeSeconds = $longitudeSeconds;

        return $this;
    }

    public function withLongitudeDirection(string $longitudeDirection): self
    {
        $this->longitudeDirection = $longitudeDirection;

        return $this;
    }

    public function build(): GpsCoordinates
    {
        if (is_null($this->latitudeDegrees)) {
            throw new RuntimeException('Latitude degrees are missing.');
        }

        if (is_null($this->latitudeMinutes)) {
            throw new RuntimeException('Latitude minutes are missing.');
        }

        if (is_null($this->latitudeSeconds)) {
            throw new RuntimeException('Latitude seconds are missing.');
        }

        if (is_null($this->latitudeDirection)) {
            throw new RuntimeException('Latitude direction is missing.');
        }

        if (is_null($this->longitudeDegrees)) {
            throw new RuntimeException('Longitude degrees are missing.');
        }

        if (is_null($this->longitudeMinutes)) {
            throw new RuntimeException('Longitude minutes are missing.');
        }

        if (is_null($this->longitudeSeconds)) {
            throw new RuntimeException('Longitude seconds are missing.');
        }

        if (is_null($this->longitudeDirection)) {
            throw new RuntimeException('Longitude direction is missing.');
        }

        return GpsCoordinates::create(
            $this->latitudeDegrees,
            $this->latitudeMinutes,
            $this->latitudeSeconds,
            $this->latitudeDirection,
            $this->longitudeDegrees,
            $this->longitudeMinutes,
            $this->longitudeSeconds,
            $this->longitudeDirection
        );
    }
}
