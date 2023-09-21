<?php
declare(strict_types=1);

namespace Hotspot\HotspotAnalysis\UseCase\FindHotspotAnalysisSummary;

use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\AnalysisId;
use Hotspot\HotspotAnalysis\Domain\HotspotAnalysisRepository\HotspotAnalysisRepository;

class FindHotspotAnalysisSummaryUseCase
{
    private HotspotAnalysisRepository $repository;

    public function __construct(HotspotAnalysisRepository $repository)
    {
        $this->repository = $repository;
    }

    /** @return array<mixed> */
    public function execute(FindHotspotAnalysisSummaryQuery $query): ?array
    {
        $analysisId = AnalysisId::fromString($query->getAnalysisId());
        $analysis = $this->repository->findAnalysisById($analysisId);

        if (is_null($analysis)) {
            return null;
        }

        $summary = [
            'analysisId' => $analysis->getId()->value(),
            'target' => $analysis->getTarget(),
            'date' => $analysis->getCreationDate()->format('Y/m/d H:i:s'),
            'numImages' => $analysis->getNumRecords(),
            'numPanels' => $analysis->getNumPanels(),
            'numHotspots' => $analysis->getNumHotspots(),
        ];

        $summary['records'] = [];
        foreach ($analysis->getRecords() as $record) {
            $recordArray = [
                'analysisId' => $record->getAnalysisId()->value(),
                'imageId' => $record->getImageId()->value(),
                'imageName' => $record->getImageName(),
                'numPanels' => $record->getNumPanels(),
                'numHotspots' => $record->getNumHotspots(),
            ];

            $coordinates = $record->getGpsCoordinates();
            if (!is_null($coordinates)) {
                $recordArray['latitude'] =
                    $coordinates->getLatitude()
                        ->degreesMinutesSecondsAndDirection();

                $recordArray['longitude'] =
                    $coordinates->getLongitude()
                        ->degreesMinutesSecondsAndDirection();
            }

            $summary['records'][] = $recordArray;
        }

        return $summary;
    }
}
