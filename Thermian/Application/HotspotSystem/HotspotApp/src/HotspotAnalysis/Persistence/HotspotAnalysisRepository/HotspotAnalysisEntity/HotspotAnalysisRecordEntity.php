<?php
declare(strict_types=1);

namespace Hotspot\HotspotAnalysis\Persistence\HotspotAnalysisRepository\HotspotAnalysisEntity;

use Hotspot\HotspotAnalysis\Domain\GpsCoordinates\GpsCoordinates;
use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\AnalysisId;
use Hotspot\HotspotDataset\Domain\Image\ImageId;
use InvalidArgumentException;

class HotspotAnalysisRecordEntity
{
    private AnalysisId $analysisId;
    private ImageId $imageId;
    private string $imageName;
    private ?GpsCoordinates $gpsCoordinates;

    /** @var array<\Hotspot\HotspotAnalysis\Persistence\HotspotRepository\HotspotEntity\HotspotEntity> */
    private array $hotspots;
    /** @var array<\Hotspot\HotspotAnalysis\Persistence\PanelRepository\PanelEntity\PanelEntity> */
    private array $panels;

    /**
     * @param array<\Hotspot\HotspotAnalysis\Persistence\HotspotRepository\HotspotEntity\HotspotEntity> $hotspots
     * @param array<\Hotspot\HotspotAnalysis\Persistence\PanelRepository\PanelEntity\PanelEntity> $panels
     */
    public function __construct(
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
            if (array_key_exists($panel->getId()->value(), $this->panels)) {
                throw new InvalidArgumentException(
                    'The panel ids in a hotspot analysis must be unique'
                );
            }

            $this->panels[$panel->getId()->value()] = $panel;
        }

        $this->hotspots = [];
        foreach ($hotspots as $hotspot) {
            if (array_key_exists($hotspot->getId()->value(), $this->hotspots)) {
                throw new InvalidArgumentException(
                    'The hotspot ids in a hotspot analysis must be unique'
                );
            }

            if (!array_key_exists($hotspot->getPanelId()->value(), $this->panels)) {
                throw new InvalidArgumentException(
                    'Hotspots must reference panel ids that exist in the analysis'
                );
            }

            $this->hotspots[$hotspot->getId()->value()] = $hotspot;
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

    /** @return array<\Hotspot\HotspotAnalysis\Persistence\PanelRepository\PanelEntity\PanelEntity> */
    public function getPanels(): array
    {
        return $this->panels;
    }

    public function getNumPanels(): int
    {
        return count($this->panels);
    }

    /** @return array<\Hotspot\HotspotAnalysis\Persistence\HotspotRepository\HotspotEntity\HotspotEntity> */
    public function getHotspots(): array
    {
        return $this->hotspots;
    }

    public function getNumHotspots(): int
    {
        return count($this->hotspots);
    }

    public function equals(HotspotAnalysisRecordEntity $other): bool
    {
        return ($this->getAnalysisId() == $other->getAnalysisId())
            && ($this->getImageId() == $other->getImageId())
            && ($this->getGpsCoordinates() == $other->getGpsCoordinates())
            && ($this->getPanels() == $other->getPanels())
            && ($this->getHotspots() == $other->getHotspots());
    }
}
