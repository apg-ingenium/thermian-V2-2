<?php
declare(strict_types=1);

namespace Hotspot\HotspotAnalysis\Domain\Panel;

class PanelBuilder
{
    private int $index;
    private float $score;
    private int $xMin;
    private int $xMax;
    private int $yMin;
    private int $yMax;

    public static function panel(): self
    {
        return new PanelBuilder();
    }

    public function withIndex(int $index): self
    {
        $this->index = $index;

        return $this;
    }

    public function withScore(float $score): self
    {
        $this->score = $score;

        return $this;
    }

    public function withXMin(int $xMin): self
    {
        $this->xMin = $xMin;

        return $this;
    }

    public function withXMax(int $xMax): self
    {
        $this->xMax = $xMax;

        return $this;
    }

    public function withYMin(int $yMin): self
    {
        $this->yMin = $yMin;

        return $this;
    }

    public function withYMax(int $yMax): self
    {
        $this->yMax = $yMax;

        return $this;
    }

    public function atPosition(int $x, int $y): self
    {
        $this->xMin = $x;
        $this->yMin = $y;

        return $this;
    }

    public function withSize(int $width, int $height): self
    {
        $this->xMax = $this->xMin + $width;
        $this->yMax = $this->yMin + $height;

        return $this;
    }

    public function build(): Panel
    {
        return Panel::create(
            $this->index,
            $this->score,
            $this->xMin,
            $this->xMax,
            $this->yMin,
            $this->yMax,
        );
    }
}
