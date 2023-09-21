<?php
declare(strict_types=1);

namespace Hotspot\HotspotAnalysis\Persistence\HotspotAnalysisRepository\HotspotAnalysisEntity;

use Hotspot\HotspotAnalysis\Domain\GpsCoordinates\GpsCoordinates;
use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\AnalysisId;
use Hotspot\HotspotAnalysis\Persistence\HotspotRepository\HotspotEntity\HotspotEntity;
use Hotspot\HotspotAnalysis\Persistence\PanelRepository\PanelEntity\PanelEntity;
use Hotspot\HotspotDataset\Domain\Image\ImageId;
use RuntimeException;

class HotspotAnalysisRecordEntityBuilder
{
    private ?AnalysisId $analysisId;
    private ?ImageId $imageId;
    private ?string $imageName;
    private ?GpsCoordinates $gpsCoordinates;

    /** @var array<\Hotspot\HotspotAnalysis\Persistence\HotspotRepository\HotspotEntity\HotspotEntity> */
    private array $hotspots;
    /** @var array<\Hotspot\HotspotAnalysis\Persistence\PanelRepository\PanelEntity\PanelEntity> */
    private array $panels;

    private function __construct()
    {
        $this->analysisId = null;
        $this->imageId = null;
        $this->imageName = null;
        $this->gpsCoordinates = null;
        $this->hotspots = [];
        $this->panels = [];
    }

    public static function hotspotAnalysisRecord(): self
    {
        return new HotspotAnalysisRecordEntityBuilder();
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
        $this->imageName = $imageName;

        return $this;
    }

    public function withGpsCoordinates(?GpsCoordinates $gpsCoordinates): self
    {
        $this->gpsCoordinates = $gpsCoordinates;

        return $this;
    }

    /** @param array<\Hotspot\HotspotAnalysis\Persistence\PanelRepository\PanelEntity\PanelEntity> $panels */
    public function withPanels(array $panels): self
    {
        foreach ($panels as $panel) {
            $this->panels[] = $panel;
        }

        return $this;
    }

    public function withPanel(PanelEntity $panel): self
    {
        $this->panels[] = $panel;

        return $this;
    }

    /** @param array<\Hotspot\HotspotAnalysis\Persistence\HotspotRepository\HotspotEntity\HotspotEntity> $hotspots */
    public function withHotspots(array $hotspots): self
    {
        foreach ($hotspots as $hotspot) {
            $this->hotspots[] = $hotspot;
        }

        return $this;
    }

    public function withHotspot(HotspotEntity $hotspot): self
    {
        $this->hotspots[] = $hotspot;

        return $this;
    }

    public function build(): HotspotAnalysisRecordEntity
    {
        if (is_null($this->imageId)) {
            throw new RuntimeException('Image id is missing');
        }

        if (is_null($this->imageName)) {
            throw new RuntimeException('Image name is missing');
        }

        return new HotspotAnalysisRecordEntity(
            $this->analysisId,
            $this->imageId,
            $this->imageName,
            $this->hotspots,
            $this->panels,
            $this->gpsCoordinates
        );
    }
}
