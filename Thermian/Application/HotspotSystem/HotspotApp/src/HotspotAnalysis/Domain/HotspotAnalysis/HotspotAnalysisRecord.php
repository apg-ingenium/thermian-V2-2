<?php
declare(strict_types=1);

namespace Hotspot\HotspotAnalysis\Domain\HotspotAnalysis;

use Hotspot\HotspotAnalysis\Domain\GpsCoordinates\GpsCoordinates;
use Hotspot\HotspotDataset\Domain\Image\ImageId;
use InvalidArgumentException;

class HotspotAnalysisRecord
{
    private AnalysisId $analysisId;
    private ImageId $imageId;
    private string $imageName;
    private ?GpsCoordinates $gpsCoordinates;

    /** @var array<\Hotspot\HotspotAnalysis\Domain\Hotspot\Hotspot> */
    private array $hotspots;
    /** @var array<\Hotspot\HotspotAnalysis\Domain\Panel\Panel> */
    private array $panels;

    /**
     * @param array<\Hotspot\HotspotAnalysis\Domain\Hotspot\Hotspot> $hotspots
     * @param array<\Hotspot\HotspotAnalysis\Domain\Panel\Panel> $panels
     */
    public static function create(
        ?AnalysisId $analysisId,
        ImageId $imageId,
        string $imageName,
        array $hotspots,
        array $panels,
        ?GpsCoordinates $gpsCoordinates = null
    ): self {
        return new HotspotAnalysisRecord($analysisId, $imageId, $imageName, $hotspots, $panels, $gpsCoordinates);
    }

    /**
     * @param array<\Hotspot\HotspotAnalysis\Domain\Hotspot\Hotspot> $hotspots
     * @param array<\Hotspot\HotspotAnalysis\Domain\Panel\Panel> $panels
     */
    private function __construct(
        ?AnalysisId $analysisId,
        ImageId $imageId,
        string $imageName,
        array $hotspots,
        array $panels,
        ?GpsCoordinates $gpsCoordinates = null
    ) {
        $this->analysisId = $analysisId ?? AnalysisId::random();
        $this->imageId = $imageId;
        $this->imageName = $imageName;
        $this->gpsCoordinates = $gpsCoordinates;

        $this->panels = [];
        foreach ($panels as $panel) {
            if (array_key_exists($panel->getIndex(), $this->panels)) {
                throw new InvalidArgumentException(
                    'Panel indices in a hotspot analysis record must be unique'
                );
            }

            $this->panels[$panel->getIndex()] = $panel;
        }

        $this->hotspots = [];
        foreach ($hotspots as $hotspot) {
            if (!array_key_exists($hotspot->getPanelIndex(), $this->panels)) {
                throw new InvalidArgumentException(
                    'Hotspots must reference panel indices that exist in the analysis record'
                );
            }

            $hotspotId = "{$hotspot->getPanelIndex()},{$hotspot->getIndex()}";
            if (array_key_exists($hotspotId, $this->hotspots)) {
                throw new InvalidArgumentException(
                    'Hotspot indices in a panel must be unique'
                );
            }

            $this->hotspots[$hotspotId] = $hotspot;
        }
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

    public function getGpsCoordinates(): ?GpsCoordinates
    {
        return $this->gpsCoordinates;
    }

    /** @return array<\Hotspot\HotspotAnalysis\Domain\Panel\Panel> */
    public function getPanels(): array
    {
        return $this->panels;
    }

    public function getNumPanels(): int
    {
        return count($this->panels);
    }

    /**
     * @return array<\Hotspot\HotspotAnalysis\Domain\Hotspot\Hotspot>
     */
    public function getHotspots(): array
    {
        return $this->hotspots;
    }

    public function getNumHotspots(): int
    {
        return count($this->hotspots);
    }

    public function equals(HotspotAnalysisRecord $other): bool
    {
        return ($this->getAnalysisId() == $other->getAnalysisId())
            && ($this->getImageId() == $other->getImageId())
            && ($this->getGpsCoordinates() == $other->getGpsCoordinates())
            && ($this->getPanels() == $other->getPanels())
            && ($this->getHotspots() == $other->getHotspots());
    }
}
