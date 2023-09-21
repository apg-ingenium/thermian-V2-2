<?php
declare(strict_types=1);

namespace Hotspot\HotspotAnalysis\Persistence\HotspotAnalysisRepository\AnalysisRecordSummary;

use Hotspot\HotspotAnalysis\Domain\GpsCoordinates\GpsCoordinates;
use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\AnalysisId;
use Hotspot\HotspotDataset\Domain\Image\ImageId;

class AnalysisRecordSummary
{
    private AnalysisId $analysisId;
    private ImageId $imageId;
    private string $imageName;
    private ?GpsCoordinates $coordinates;
    private int $numPanels;
    private int $numHotspots;

    public static function create(
        AnalysisId $analysisId,
        ImageId $imageId,
        string $imageName,
        int $numPanels,
        int $numHotspots,
        ?GpsCoordinates $coordinates = null
    ): self {
        return new AnalysisRecordSummary($analysisId, $imageId, $imageName, $numPanels, $numHotspots, $coordinates);
    }

    private function __construct(
        AnalysisId $analysisId,
        ImageId $imageId,
        string $imageName,
        int $numPanels,
        int $numHotspots,
        ?GpsCoordinates $coordinates = null
    ) {
        $this->analysisId = $analysisId;
        $this->imageId = $imageId;
        $this->imageName = $imageName;
        $this->numPanels = $numPanels;
        $this->numHotspots = $numHotspots;
        $this->coordinates = $coordinates;
    }

    public function getAnalysisId(): AnalysisId
    {
        return $this->analysisId;
    }

    public function getImageId(): ImageId
    {
        return $this->imageId;
    }

    public function getImageName(): string
    {
        return $this->imageName;
    }

    public function getCoordinates(): ?GpsCoordinates
    {
        return $this->coordinates;
    }

    public function getNumPanels(): int
    {
        return $this->numPanels;
    }

    public function getNumHotspots(): int
    {
        return $this->numHotspots;
    }
}
