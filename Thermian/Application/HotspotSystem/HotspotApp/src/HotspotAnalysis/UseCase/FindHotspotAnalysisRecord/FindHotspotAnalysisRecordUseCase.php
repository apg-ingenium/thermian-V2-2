<?php
declare(strict_types=1);

namespace Hotspot\HotspotAnalysis\UseCase\FindHotspotAnalysisRecord;

use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\AnalysisId;
use Hotspot\HotspotAnalysis\Domain\HotspotAnalysisRepository\HotspotAnalysisRepository;
use Hotspot\HotspotDataset\Domain\Image\ImageId;

class FindHotspotAnalysisRecordUseCase
{
    private HotspotAnalysisRepository $repository;

    public function __construct(HotspotAnalysisRepository $repository)
    {
        $this->repository = $repository;
    }

    /** @return array<string, mixed> */
    public function execute(FindHotspotAnalysisRecordQuery $query): array
    {
        $analysisId = AnalysisId::fromString($query->getAnalysisId());
        $imageId = ImageId::fromString($query->getImageId());

        $record = $this->repository->findAnalysisRecordById($analysisId, $imageId);

        if (is_null($record)) {
            return [];
        }

        $recordView = [
            'analysisId' => $record->getAnalysisId()->value(),
            'imageId' => $record->getImageId()->value(),
            'imageName' => $record->getImageName(),
            'numPanels' => $record->getNumPanels(),
            'numHotspots' => $record->getNumHotspots(),
        ];

        $coordinates = $record->getGpsCoordinates();

        if (!is_null($coordinates)) {
            $recordView['latitude'] =
                $coordinates->getLatitude()
                    ->degreesMinutesSecondsAndDirection();

            $recordView['longitude'] =
                $coordinates->getLongitude()
                    ->degreesMinutesSecondsAndDirection();
        }

        $panels = [];
        foreach ($record->getPanels() as $panel) {
            $panels[$panel->getIndex()] = [
                'index' => $panel->getIndex(),
                'score' => $panel->getScore(),
                'xMin' => $panel->getXMin(),
                'yMin' => $panel->getYMin(),
                'xMax' => $panel->getXMax(),
                'yMax' => $panel->getYMax(),
                'hotspots' => [],
            ];
        }

        foreach ($record->getHotspots() as $hotspot) {
            $panelIndex = $hotspot->getPanelIndex();
            $panels[$panelIndex]['hotspots'][$hotspot->getIndex()] = [
                'index' => $hotspot->getIndex(),
                'score' => $hotspot->getScore(),
                'xMin' => $hotspot->getXMin(),
                'yMin' => $hotspot->getYMin(),
                'xMax' => $hotspot->getXMax(),
                'yMax' => $hotspot->getYMax(),
            ];
        }

        foreach ($panels as &$panel) {
            $hotspots = &$panel['hotspots'];
            $numHotspots = count($hotspots);
            if ($numHotspots > 0) {
                $hotspots = array_map(fn($index) => $hotspots[$index], range(1, $numHotspots));
            }
        }

        $numPanels = count($panels);

        $recordView['panels'] = $numPanels > 0
            ? array_map(fn($index) => $panels[$index], range(1, count($panels)))
            : [];

        return $recordView;
    }
}
