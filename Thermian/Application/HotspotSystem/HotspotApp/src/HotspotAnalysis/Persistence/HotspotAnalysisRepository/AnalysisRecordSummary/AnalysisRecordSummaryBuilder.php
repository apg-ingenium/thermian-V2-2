<?php
declare(strict_types=1);

namespace Hotspot\HotspotAnalysis\Persistence\HotspotAnalysisRepository\AnalysisRecordSummary;

use Hotspot\HotspotAnalysis\Domain\GpsCoordinates\GpsCoordinates;
use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\AnalysisId;
use Hotspot\HotspotDataset\Domain\Image\ImageId;
use RuntimeException;

class AnalysisRecordSummaryBuilder
{
    public static function analysisRecordSummary(): self
    {
        return new AnalysisRecordSummaryBuilder();
    }

    private ?AnalysisId $analysisId;
    private ?ImageId $imageId;
    private ?string $imageName;
    private ?int $numPanels;
    private ?int $numHotspots;
    private ?GpsCoordinates $coordinates;

    public function __construct()
    {
        $this->analysisId = null;
        $this->imageId = null;
        $this->imageName = null;
        $this->numPanels = null;
        $this->numHotspots = null;
        $this->coordinates = null;
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

    public function withNumPanels(int $numPanels): self
    {
        $this->numPanels = $numPanels;

        return $this;
    }

    public function withNumHotspots(int $numHotspots): self
    {
        $this->numHotspots = $numHotspots;

        return $this;
    }

    public function withGpsCoordinates(?GpsCoordinates $coordinates): self
    {
        $this->coordinates = $coordinates;

        return $this;
    }

    public function build(): AnalysisRecordSummary
    {
        if (is_null($this->analysisId)) {
            throw new RuntimeException('Analysis id is missing');
        }

        if (is_null($this->imageId)) {
            throw new RuntimeException('Image id is missing');
        }

        if (is_null($this->imageName)) {
            throw new RuntimeException('Image name is missing');
        }

        if (is_null($this->numPanels)) {
            throw new RuntimeException('The number of panels is missing');
        }

        if (is_null($this->numHotspots)) {
            throw new RuntimeException('The number of hotspots is missing');
        }

        return AnalysisRecordSummary::create(
            $this->analysisId,
            $this->imageId,
            $this->imageName,
            $this->numPanels,
            $this->numHotspots,
            $this->coordinates
        );
    }
}
