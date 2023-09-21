<?php
declare(strict_types=1);

namespace Hotspot\HotspotResults\UseCase\FindHotspotCsv;

use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\AnalysisId;
use Hotspot\HotspotDataset\Domain\Image\ImageId;
use Hotspot\HotspotResults\Domain\HotspotCsv\HotspotCsv;
use Hotspot\HotspotResults\Domain\HotspotCsvRepository\HotspotCsvRepository;

class FindHotspotCsvUseCase
{
    private HotspotCsvRepository $repository;

    public function __construct(HotspotCsvRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(FindHotspotCsvQuery $query): ?HotspotCsv
    {
        $analysisId = AnalysisId::fromString($query->getAnalysisId());
        $imageId = ImageId::fromString($query->getImageId());
        $csvName = 'hotspots.csv';

        return $this->repository->findByRecordIdAndName($analysisId, $imageId, $csvName);
    }
}
