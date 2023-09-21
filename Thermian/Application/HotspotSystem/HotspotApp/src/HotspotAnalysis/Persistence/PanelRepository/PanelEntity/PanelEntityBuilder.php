<?php
declare(strict_types=1);

namespace Hotspot\HotspotAnalysis\Persistence\PanelRepository\PanelEntity;

use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\AnalysisId;
use Hotspot\HotspotDataset\Domain\Image\ImageId;
use Shared\Domain\Uuid;

class PanelEntityBuilder
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

    public static function panel(): self
    {
        return new PanelEntityBuilder();
    }

    public function withId(Uuid $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function withAnalysisId(AnalysisId $analysisId): self
    {
        $this->analysisId = $analysisId;

        return $this;
    }

    public function withImageId(ImageId $imageId): self
    {
        $this->imageId = $imageId;

        return $this;
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

    public function build(): PanelEntity
    {
        return PanelEntity::create(
            $this->analysisId,
            $this->imageId,
            $this->index,
            $this->score,
            $this->xMin,
            $this->xMax,
            $this->yMin,
            $this->yMax,
            $this->id
        );
    }
}
