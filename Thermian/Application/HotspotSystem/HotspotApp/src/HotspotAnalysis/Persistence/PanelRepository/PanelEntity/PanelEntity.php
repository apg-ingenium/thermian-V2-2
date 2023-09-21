<?php
declare(strict_types=1);

namespace Hotspot\HotspotAnalysis\Persistence\PanelRepository\PanelEntity;

use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\AnalysisId;
use Hotspot\HotspotDataset\Domain\Image\ImageId;
use Shared\Domain\Uuid;

class PanelEntity
{
    private Uuid $id;
    private AnalysisId $analysisId;
    private ImageId $imageId;
    private int $index;
    private float $score;
    private int $xMin;
    private int $xMax;
    private int $yMin;
    private int $yMax;

    public static function create(
        AnalysisId $analysisId,
        ImageId $imageId,
        int $index,
        float $score,
        int $xMin,
        int $xMax,
        int $yMin,
        int $yMax,
        Uuid $id
    ): PanelEntity {
        return new PanelEntity(
            $analysisId,
            $imageId,
            $index,
            $score,
            $xMin,
            $xMax,
            $yMin,
            $yMax,
            $id
        );
    }

    private function __construct(
        AnalysisId $analysisId,
        ImageId $imageId,
        int $index,
        float $score,
        int $xMin,
        int $xMax,
        int $yMin,
        int $yMax,
        ?Uuid $id = null
    ) {
        $this->id = $id ?? Uuid::random();
        $this->analysisId = $analysisId;
        $this->imageId = $imageId;
        $this->index = $index;
        $this->score = $score;
        $this->xMin = $xMin;
        $this->xMax = $xMax;
        $this->yMin = $yMin;
        $this->yMax = $yMax;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getAnalysisId(): AnalysisId
    {
        return $this->analysisId;
    }

    public function getImageId(): ImageId
    {
        return $this->imageId;
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

    public function equivalent(PanelEntity $other): bool
    {
        return ($this->getIndex() == $other->getIndex())
            && ($this->getScore() == $other->getScore())
            && ($this->getXMin() === $other->getXMin())
            && ($this->getXMax() === $other->getXMax())
            && ($this->getYMin() === $other->getYMin())
            && ($this->getYMax() === $other->getYMax());
    }
}
