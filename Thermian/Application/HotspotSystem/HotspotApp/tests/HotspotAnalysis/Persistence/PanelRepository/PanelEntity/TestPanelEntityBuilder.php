<?php
declare(strict_types=1);

namespace Hotspot\Test\HotspotAnalysis\Persistence\PanelRepository\PanelEntity;

use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\AnalysisId;
use Hotspot\HotspotAnalysis\Persistence\PanelRepository\PanelEntity\PanelEntity;
use Hotspot\HotspotAnalysis\Persistence\PanelRepository\PanelEntity\PanelEntityBuilder;
use Hotspot\HotspotDataset\Domain\Image\ImageId;
use Shared\Domain\Uuid;

class TestPanelEntityBuilder
{
    public static function random(): self
    {
        return self::panel()
            ->withId(Uuid::random())
            ->withAnalysisId(AnalysisId::random())
            ->withImageId(ImageId::random())
            ->withIndex(rand(1, 50))
            ->withScore(rand(0, 101) / 100)
            ->withRandomBox();
    }

    public static function panel(): self
    {
        return new TestPanelEntityBuilder();
    }

    private PanelEntityBuilder $builder;

    public function __construct()
    {
        $this->builder = PanelEntityBuilder::panel();
    }

    public function withId(Uuid $id): self
    {
        $this->builder->withId($id);

        return $this;
    }

    public function withAnalysisId(AnalysisId $analysisId): self
    {
        $this->builder->withAnalysisId($analysisId);

        return $this;
    }

    public function withImageId(ImageId $imageId): self
    {
        $this->builder->withImageId($imageId);

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

    public function build(): PanelEntity
    {
        return $this->builder->build();
    }
}
