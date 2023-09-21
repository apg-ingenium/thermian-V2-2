<?php
declare(strict_types=1);

namespace Hotspot\HotspotAnalysis\Domain\Panel;

class Panel
{
    private int $index;
    private float $score;
    private int $xMin;
    private int $xMax;
    private int $yMin;
    private int $yMax;

    public static function create(
        int $index,
        float $score,
        int $xMin,
        int $xMax,
        int $yMin,
        int $yMax,
    ): Panel {
        return new Panel(
            $index,
            $score,
            $xMin,
            $xMax,
            $yMin,
            $yMax,
        );
    }

    private function __construct(
        int $index,
        float $score,
        int $xMin,
        int $xMax,
        int $yMin,
        int $yMax,
    ) {
        $this->index = $index;
        $this->score = $score;
        $this->xMin = $xMin;
        $this->xMax = $xMax;
        $this->yMin = $yMin;
        $this->yMax = $yMax;
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    public function getScore(): float
    {
        return $this->score;
    }

    public function getXMin(): int
    {
        return $this->xMin;
    }

    public function getXMax(): int
    {
        return $this->xMax;
    }

    public function getYMin(): int
    {
        return $this->yMin;
    }

    public function getYMax(): int
    {
        return $this->yMax;
    }

    public function equals(Panel $other): bool
    {
        return ($this->getIndex() == $other->getIndex())
            && ($this->getScore() == $other->getScore())
            && ($this->getXMin() === $other->getXMin())
            && ($this->getXMax() === $other->getXMax())
            && ($this->getYMin() === $other->getYMin())
            && ($this->getYMax() === $other->getYMax());
    }
}
