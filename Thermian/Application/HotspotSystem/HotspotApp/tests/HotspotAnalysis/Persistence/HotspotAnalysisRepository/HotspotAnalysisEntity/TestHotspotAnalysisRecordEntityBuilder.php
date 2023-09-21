<?php
declare(strict_types=1);

namespace Hotspot\Test\HotspotAnalysis\Persistence\HotspotAnalysisRepository\HotspotAnalysisEntity;

use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\AnalysisId;
use Hotspot\HotspotAnalysis\Persistence\HotspotAnalysisRepository\HotspotAnalysisEntity\HotspotAnalysisRecordEntity;
use Hotspot\HotspotAnalysis\Persistence\HotspotAnalysisRepository\HotspotAnalysisEntity\HotspotAnalysisRecordEntityBuilder;
use Hotspot\HotspotAnalysis\Persistence\HotspotRepository\HotspotEntity\HotspotEntity;
use Hotspot\HotspotDataset\Domain\Image\ImageId;
use Hotspot\Test\HotspotAnalysis\Persistence\HotspotRepository\HotspotEntity\TestHotspotEntityBuilder;
use Hotspot\Test\HotspotAnalysis\Persistence\PanelRepository\PanelEntity\TestPanelEntityBuilder;
use Shared\Domain\Uuid;

class TestHotspotAnalysisRecordEntityBuilder
{
    private HotspotAnalysisRecordEntityBuilder $builder;
    private AnalysisId $analysisId;
    private ImageId $imageId;

    private function __construct()
    {
        $this->analysisId = AnalysisId::random();
        $this->imageId = ImageId::random();

        $randomIndex = mt_rand(0, 1000000);
        $randomImageName = "image-{$randomIndex}.jpeg";
        $this->builder = HotspotAnalysisRecordEntityBuilder
            ::hotspotAnalysisRecord()
            ->withImageName($randomImageName);
    }

    public static function hotspotAnalysisRecord(): self
    {
        return new TestHotspotAnalysisRecordEntityBuilder();
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

    public function withHotspot(HotspotEntity $hotspot): self
    {
        $this->builder->withHotspot($hotspot);

        return $this;
    }

    public function withRandomHotspots(int $numPanels = 3, int $numHotspotsPerPanel = 3): self
    {
        for ($panelIndex = 0; $panelIndex < $numPanels; $panelIndex++) {
            $panelId = Uuid::random();
            $this->builder->withPanel(
                TestPanelEntityBuilder::random()
                    ->withId($panelId)
                    ->withAnalysisId($this->analysisId)
                    ->withImageId($this->imageId)
                    ->withIndex($panelIndex)
                    ->build()
            );

            for ($hotspot_index = 0; $hotspot_index < $numHotspotsPerPanel; $hotspot_index++) {
                $this->builder->withHotspot(
                    TestHotspotEntityBuilder::random()
                        ->withPanelId($panelId)
                        ->withIndex($hotspot_index)
                        ->build()
                );
            }
        }

        return $this;
    }

    public function build(): HotspotAnalysisRecordEntity
    {
        return $this->builder
            ->withAnalysisId($this->analysisId)
            ->withImageId($this->imageId)
            ->build();
    }
}
