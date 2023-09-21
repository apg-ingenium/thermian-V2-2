<?php
declare(strict_types=1);

namespace Hotspot\HotspotAnalysis\Domain\GpsCoordinates;

class GpsCoordinates
{
    private Latitude $latitude;
    private Longitude $longitude;

    public static function fromLatitudeAndLongitude(Latitude $latitude, Longitude $longitude): self
    {
        return new GpsCoordinates(
            $latitude->degrees(),
            $latitude->minutes(),
            $latitude->seconds(),
            $latitude->direction(),
            $longitude->degrees(),
            $longitude->minutes(),
            $longitude->seconds(),
            $longitude->direction(),
        );
    }

    public static function create(
        int $latitudeDegrees,
        int $latitudeMinutes,
        float $latitudeSeconds,
        string $latitudeDirection,
        int $longitudeDegrees,
        int $longitudeMinutes,
        float $longitudeSeconds,
        string $longitudeDirection
    ): GpsCoordinates {
        return new GpsCoordinates(
            $latitudeDegrees,
            $latitudeMinutes,
            $latitudeSeconds,
            $latitudeDirection,
            $longitudeDegrees,
            $longitudeMinutes,
            $longitudeSeconds,
            $longitudeDirection
        );
    }

    private function __construct(
        int $latitudeDegrees,
        int $latitudeMinutes,
        float $latitudeSeconds,
        string $latitudeDirection,
        int $longitudeDegrees,
        int $longitudeMinutes,
        float $longitudeSeconds,
        string $longitudeDirection
    ) {
        $this->latitude = Latitude::fromAngularPosition($latitudeDegrees, $latitudeMinutes, $latitudeSeconds, $latitudeDirection);
        $this->longitude = Longitude::fromAngularPosition($longitudeDegrees, $longitudeMinutes, $longitudeSeconds, $longitudeDirection);
    }

    public function getLatitude(): Latitude
    {
        return $this->latitude;
    }

    public function getLongitude(): Longitude
    {
        return $this->longitude;
    }

    public function equals(GpsCoordinates $other): bool
    {
        return ($this->latitude === $other->latitude)
            && ($this->longitude === $other->longitude);
    }
}
