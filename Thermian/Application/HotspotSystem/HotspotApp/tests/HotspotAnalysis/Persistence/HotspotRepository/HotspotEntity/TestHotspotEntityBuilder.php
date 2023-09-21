<?php
declare(strict_types=1);

namespace Hotspot\Test\HotspotAnalysis\Persistence\HotspotRepository\HotspotEntity;

use Hotspot\HotspotAnalysis\Domain\Hotspot\HotspotId;
use Hotspot\HotspotAnalysis\Persistence\HotspotRepository\HotspotEntity\HotspotEntity;
use Hotspot\HotspotAnalysis\Persistence\HotspotRepository\HotspotEntity\HotspotEntityBuilder;
use Shared\Domain\Uuid;

class TestHotspotEntityBuilder
{
    public static function random(): self
    {
        return self::hotspot()
            ->withId(HotspotId::random())
            ->withIndex(rand(1, 50))
            ->withScore(rand(0, 101) / 100)
            ->withRandomBox();
    }

    private HotspotEntityBuilder $builder;

    public static function hotspot(): self
    {
        return new TestHotspotEntityBuilder();
    }

    public function __construct()
    {
        $this->builder = HotspotEntityBuilder::hotspot();
    }

    public function withId(HotspotId $hotspotId): self
    {
        $this->builder->withId($hotspotId);

        return $this;
    }

    public function withPanelId(Uuid $panelId): self
    {
        $this->builder->withPanelId($panelId);

        return $this;
    }

    public function withIndex(int $index): self
    {
        $this->builder->withIndex($index);

        return $this;
    }

    public function withScore(float $score): self
    {
        $this->builder->withScore($score);

        return $this;
    }

    public function withRandomBox(): self
    {
        $xMin = rand(0, 256);
        $xMax = $xMin + rand(25, 75);
        $yMin = rand(0, 256);
        $yMax = $yMin + rand(25, 75);

        $this->withXMin($xMin);
        $this->withXMax($xMax);
        $this->withYMin($yMin);
        $this->withYMax($yMax);

        return $this;
    }

    private function withXMin(int $xMin): self
    {
        $this->builder->withXMin($xMin);

        return $this;
    }

    private function withXMax(int $xMax): self
    {
        $this->builder->withXMax($xMax);

        return $this;
    }

    private function withYMin(int $yMin): self
    {
        $this->builder->withYMin($yMin);

        return $this;
    }

    private function withYMax(int $yMax): self
    {
        $this->builder->withYMax($yMax);

        return $this;
    }

    public function atPosition(int $x, int $y): self
    {
        $this->builder->atPosition($x, $y);

        return $this;
    }

    public function withSize(int $width, int $height): self
    {
        $this->builder->withSize($width, $height);

        return $this;
    }

    public function build(): HotspotEntity
    {
        return $this->builder->build();
    }
}
