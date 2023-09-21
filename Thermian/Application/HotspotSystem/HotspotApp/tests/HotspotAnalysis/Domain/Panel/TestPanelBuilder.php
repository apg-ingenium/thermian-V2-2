<?php
declare(strict_types=1);

namespace Hotspot\Test\HotspotAnalysis\Domain\Panel;

use Hotspot\HotspotAnalysis\Domain\Panel\Panel;
use Hotspot\HotspotAnalysis\Domain\Panel\PanelBuilder;

class TestPanelBuilder
{
    public static function random(): self
    {
        return self::panel()
            ->withIndex(rand(1, 50))
            ->withScore(rand(0, 101) / 100)
            ->withRandomBox();
    }

    public static function panel(): self
    {
        return new TestPanelBuilder();
    }

    private PanelBuilder $builder;

    public function __construct()
    {
        $this->builder = PanelBuilder::panel();
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
        $this->withxMax($xMax);
        $this->withYMin($yMin);
        $this->withYMax($yMax);

        return $this;
    }

    public function withXMin(int $xMin): self
    {
        $this->builder->withXMin($xMin);

        return $this;
    }

    public function withXMax(int $xMax): self
    {
        $this->builder->withXMax($xMax);

        return $this;
    }

    public function withYMin(int $yMin): self
    {
        $this->builder->withYMin($yMin);

        return $this;
    }

    public function withYMax(int $yMax): self
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

    public function build(): Panel
    {
        return $this->builder->build();
    }
}
