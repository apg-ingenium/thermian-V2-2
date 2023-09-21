<?php
declare(strict_types=1);

namespace Hotspot\HotspotAnalysis\Domain\Hotspot;

class Hotspot
{
    public static function create(
        int $index,
        int $panelIndex,
        float $score,
        int $xMin,
        int $xMax,
        int $yMin,
        int $yMax,
    ): Hotspot {
        return new Hotspot(
            $index,
            $panelIndex,
            $score,
            $xMin,
            $xMax,
            $yMin,
            $yMax,
        );
    }

    private int $index;
    private int $panelIndex;
    private float $score;
    private int $xMin;
    private int $xMax;
    private int $yMin;
    private int $yMax;

    public function __construct(
        int $index,
        int $panelIndex,
        float $score,
        int $xMin,
        int $xMax,
        int $yMin,
        int $yMax,
    ) {
        $this->index = $index;
        $this->panelIndex = $panelIndex;
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

    public function getPanelIndex(): int
    {
        return $this->panelIndex;
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

    public function equals(Hotspot $other): bool
    {
        return ($this->getIndex() == $other->getIndex())
            && ($this->getPanelIndex() == $other->getPanelIndex())
            && ($this->getScore() == $other->getScore())
            && ($this->getXMin() === $other->getXMin())
            && ($this->getXMax() === $other->getXMax())
            && ($this->getYMin() === $other->getYMin())
            && ($this->getYMax() === $other->getYMax());
    }
}
