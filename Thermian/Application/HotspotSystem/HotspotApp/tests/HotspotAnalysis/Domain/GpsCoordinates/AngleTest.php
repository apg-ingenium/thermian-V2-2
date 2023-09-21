<?php
declare(strict_types=1);

namespace Hotspot\Test\HotspotAnalysis\Domain\GpsCoordinates;

use Hotspot\HotspotAnalysis\Domain\GpsCoordinates\Angle;
use PHPUnit\Framework\TestCase;

class AngleTest extends TestCase
{
    public function testPositiveAngleFromDegreesMinutesAndSeconds(): void
    {
        $angle = Angle::fromParts(10, 42, 22.553);
        $this->assertEquals(10, $angle->degrees());
        $this->assertEquals(42, $angle->minutes());
        $this->assertEqualsWithDelta(22.553, $angle->seconds(), 5e-4);
        $this->assertEqualsWithDelta(10.70626472, $angle->decimalDegrees(), 1e-8);
    }

    public function testNegativeAngleFromDegreesMinutesAndSeconds(): void
    {
        $angle = Angle::fromParts(-125, -5, -56.268);
        $this->assertEquals(-125, $angle->degrees());
        $this->assertEquals(-5, $angle->minutes());
        $this->assertEqualsWithDelta(-56.268, $angle->seconds(), 5e-4);
        $this->assertEqualsWithDelta(-125.09896333, $angle->decimalDegrees(), 1e-8);
    }

    public function testPositiveAngleFromDecimalDegrees(): void
    {
        $angle = Angle::fromDecimalDegrees(10.70626472);
        $this->assertEquals(10, $angle->degrees());
        $this->assertEquals(42, $angle->minutes());
        $this->assertEqualsWithDelta(22.553, $angle->seconds(), 5e-4);
        $this->assertEqualsWithDelta(10.70626472, $angle->decimalDegrees(), 1e-8);
    }

    public function testNegativeAngleFromDecimalDegrees(): void
    {
        $angle = Angle::fromDecimalDegrees(-125.0989633);
        $this->assertEquals(-125, $angle->degrees());
        $this->assertEquals(-5, $angle->minutes());
        $this->assertEqualsWithDelta(-56.268, $angle->seconds(), 5e-4);
        $this->assertEqualsWithDelta(-125.0989633, $angle->decimalDegrees(), 1e-8);
    }

    public function testAngleEquality(): void
    {
        $x = Angle::fromParts(90, 0, 0);
        $y = Angle::fromParts(-270, 0, 0);
        $z = Angle::fromParts(360 + 90, 0, 0);
        $r = Angle::fromParts(180, 0, 0);

        $this->assertObjectEquals($x, $y);
        $this->assertObjectEquals($x, $z);
        $this->assertFalse($x->equals($r));
    }
}
