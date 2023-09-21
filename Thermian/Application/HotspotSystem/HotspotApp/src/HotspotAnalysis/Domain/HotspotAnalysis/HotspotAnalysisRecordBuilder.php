<?php
declare(strict_types=1);

namespace Hotspot\HotspotAnalysis\Domain\HotspotAnalysis;

use Hotspot\HotspotAnalysis\Domain\GpsCoordinates\GpsCoordinates;
use Hotspot\HotspotAnalysis\Domain\Hotspot\Hotspot;
use Hotspot\HotspotAnalysis\Domain\Panel\Panel;
use Hotspot\HotspotDataset\Domain\Image\ImageId;
use RuntimeException;

class HotspotAnalysisRecordBuilder
{
    private ?AnalysisId $analysisId;
    private ?ImageId $imageId;
    private ?string $imageName;
    private ?GpsCoordinates $gpsCoordinates;

    /** @var array<\Hotspot\HotspotAnalysis\Domain\Hotspot\Hotspot> */
    private array $hotspots;
    /** @var array<\Hotspot\HotspotAnalysis\Domain\Panel\Panel> */
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
        return new HotspotAnalysisRecordBuilder();
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

    public function withPanel(Panel $panel): self
    {
        $this->panels[] = $panel;

        return $this;
    }

    /** @param array<\Hotspot\HotspotAnalysis\Domain\Panel\Panel> $panels */
    public function withPanels(array $panels): self
    {
        foreach ($panels as $panel) {
            $this->panels[] = $panel;
        }

        return $this;
    }

    public function withHotspot(Hotspot $hotspot): self
    {
        $this->hotspots[] = $hotspot;

        return $this;
    }

    /** @param array<\Hotspot\HotspotAnalysis\Domain\Hotspot\Hotspot> $hotspots */
    public function withHotspots(array $hotspots): self
    {
        foreach ($hotspots as $hotspot) {
            $this->hotspots[] = $hotspot;
        }

        return $this;
    }

    public function build(): HotspotAnalysisRecord
    {
        if (is_null($this->imageId)) {
            throw new RuntimeException('Image id is missing');
        }

        if (is_null($this->imageName)) {
            throw new RuntimeException('Image name is missing');
        }

        return HotspotAnalysisRecord::create(
            $this->analysisId,
            $this->imageId,
            $this->imageName,
            $this->hotspots,
            $this->panels,
            $this->gpsCoordinates,
        );
    }
}
