<?php
declare(strict_types=1);

namespace Hotspot\HotspotAnalysis\Persistence\HotspotRepository\HotspotEntity;

use Hotspot\HotspotAnalysis\Domain\Hotspot\HotspotId;
use Shared\Domain\Uuid;

class HotspotEntity
{
    public static function create(
        Uuid $panelId,
        int $index,
        float $score,
        int $xMin,
        int $xMax,
        int $yMin,
        int $yMax,
        ?HotspotId $id = null
    ): HotspotEntity {
        return new HotspotEntity(
            $panelId,
            $index,
            $score,
            $xMin,
            $xMax,
            $yMin,
            $yMax,
            $id
        );
    }

    private Uuid $panelId;
    private int $index;
    private float $score;
    private int $xMin;
    private int $xMax;
    private int $yMin;
    private int $yMax;
    private HotspotId $id;

    public function __construct(
        Uuid $panelId,
        int $index,
        float $score,
        int $xMin,
        int $xMax,
        int $yMin,
        int $yMax,
        ?HotspotId $id = null
    ) {
        $this->id = $id ?? HotspotId::random();
        $this->panelId = $panelId;
        $this->index = $index;
        $this->score = $score;
        $this->xMin = $xMin;
        $this->xMax = $xMax;
        $this->yMin = $yMin;
        $this->yMax = $yMax;
    }

    public function getId(): HotspotId
    {
        return $this->id;
    }

    public function getPanelId(): Uuid
    {
        return $this->panelId;
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

    public function equivalent(HotspotEntity $other): bool
    {
        return ($this->getIndex() == $other->getIndex())
            && ($this->getScore() == $other->getScore())
            && ($this->getXMin() === $other->getXMin())
            && ($this->getXMax() === $other->getXMax())
            && ($this->getYMin() === $other->getYMin())
            && ($this->getYMax() === $other->getYMax());
    }
}
