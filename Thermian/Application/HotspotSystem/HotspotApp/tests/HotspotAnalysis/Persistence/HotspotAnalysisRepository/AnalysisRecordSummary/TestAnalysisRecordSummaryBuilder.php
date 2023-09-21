<?php
declare(strict_types=1);

namespace Hotspot\Test\HotspotAnalysis\Persistence\HotspotAnalysisRepository\AnalysisRecordSummary;

use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\AnalysisId;
use Hotspot\HotspotAnalysis\Persistence\HotspotAnalysisRepository\AnalysisRecordSummary\AnalysisRecordSummary;
use Hotspot\HotspotAnalysis\Persistence\HotspotAnalysisRepository\AnalysisRecordSummary\AnalysisRecordSummaryBuilder;
use Hotspot\HotspotDataset\Domain\Image\ImageId;

class TestAnalysisRecordSummaryBuilder
{
    public static function random(): self
    {
        $randomIndex = mt_rand(0, 1000000);
        $randomImageName = "image-{$randomIndex}.jpg";
        $randomNumPanels = mt_rand(0, 20);
        $randomNumHotspots = mt_rand(0, 300);

        return TestAnalysisRecordSummaryBuilder
            ::analysisRecordSummary()
            ->withAnalysisId(AnalysisId::random())
            ->withImageId(ImageId::random())
            ->withImageName($randomImageName)
            ->withNumPanels($randomNumPanels)
            ->withNumHotspots($randomNumHotspots);
    }

    public static function analysisRecordSummary(): self
    {
        return new TestAnalysisRecordSummaryBuilder();
    }

    private AnalysisRecordSummaryBuilder $builder;

    private function __construct()
    {
        $this->builder = AnalysisRecordSummaryBuilder
            ::analysisRecordSummary()
            ->withAnalysisId(AnalysisId::fromString('48b9bba7-5995-40da-aecd-82efe3e20aff'))
            ->withImageId(ImageId::fromString('80a6147c-0b10-4611-9b73-8a9689b21349'));
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

    public function withImageName(string $imageName): self
    {
        $this->builder->withImageName($imageName);

        return $this;
    }

    public function withNumPanels(int $numPanels): self
    {
        $this->builder->withNumPanels($numPanels);

        return $this;
    }

    public function withNumHotspots(int $numHotspots): self
    {
        $this->builder->withNumHotspots($numHotspots);

        return $this;
    }

    public function build(): AnalysisRecordSummary
    {
        return $this->builder->build();
    }
}
