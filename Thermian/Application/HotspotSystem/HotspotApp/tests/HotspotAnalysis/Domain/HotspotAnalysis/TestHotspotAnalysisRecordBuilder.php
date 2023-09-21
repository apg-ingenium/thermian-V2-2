<?php
declare(strict_types=1);

namespace Hotspot\Test\HotspotAnalysis\Domain\HotspotAnalysis;

use Hotspot\HotspotAnalysis\Domain\GpsCoordinates\GpsCoordinates;
use Hotspot\HotspotAnalysis\Domain\Hotspot\Hotspot;
use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\AnalysisId;
use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\HotspotAnalysisRecord;
use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\HotspotAnalysisRecordBuilder;
use Hotspot\HotspotAnalysis\Domain\Panel\Panel;
use Hotspot\HotspotDataset\Domain\Image\ImageId;
use Hotspot\Test\HotspotAnalysis\Domain\Hotspot\TestHotspotBuilder;
use Hotspot\Test\HotspotAnalysis\Domain\Panel\TestPanelBuilder;

class TestHotspotAnalysisRecordBuilder
{
    private HotspotAnalysisRecordBuilder $builder;
    private AnalysisId $analysisId;
    private ImageId $imageId;
    private int $numPanels = 0;

    private function __construct()
    {
        $randomIndex = mt_rand(0, 1000000000);
        $randomImageName = "image-${randomIndex}.png";
        $this->analysisId = AnalysisId::random();
        $this->imageId = ImageId::random();
        $this->builder = HotspotAnalysisRecordBuilder
            ::hotspotAnalysisRecord()
            ->withImageName($randomImageName);
    }

    public static function hotspotAnalysisRecord(): self
    {
        return new TestHotspotAnalysisRecordBuilder();
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

    public function withImageName(string $imageName): self
    {
        $this->builder->withImageName($imageName);

        return $this;
    }

    public function withGpsCoordinates(GpsCoordinates $gpsCoordinates): self
    {
        $this->builder->withGpsCoordinates($gpsCoordinates);

        return $this;
    }

    public function withPanel(Panel $panel): self
    {
        $this->builder->withPanel($panel);

        return $this;
    }

    public function withHotspot(Hotspot $hotspot): self
    {
        $this->builder->withHotspot($hotspot);

        return $this;
    }

    public function withRandomHotspots(int $numHotspots = 3): self
    {
        $panelIndex = ++$this->numPanels;

        $this->builder->withPanel(
            TestPanelBuilder::random()
            ->withIndex($panelIndex)
            ->build()
        );

        for ($index = 0; $index < $numHotspots; $index++) {
            $this->builder->withHotspot(
                TestHotspotBuilder::random()
                    ->withIndex($index)
                    ->withPanelIndex($panelIndex)
                    ->build()
            );
        }

        return $this;
    }

    public function build(): HotspotAnalysisRecord
    {
        return $this->builder
            ->withAnalysisId($this->analysisId)
            ->withImageId($this->imageId)
            ->build();
    }
}
