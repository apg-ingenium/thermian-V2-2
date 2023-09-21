<?php
declare(strict_types=1);

namespace Hotspot\HotspotResults\UseCase\FindOutputCsv;

use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\AnalysisId;
use Hotspot\HotspotDataset\Domain\Image\ImageId;
use Hotspot\HotspotResults\Domain\OutputCsvRepository\OutputCsvRepository;

class FindOutputCsvForAnalysisRecordUseCase
{
    private OutputCsvRepository $repository;

    public function __construct(OutputCsvRepository $repository)
    {
        $this->repository = $repository;
    }

    /** @return array<\Hotspot\HotspotResults\Domain\HotspotCsv\HotspotCsv> */
    public function execute(FindOutputCsvForAnalysisRecordQuery $query): array
    {
        $analysisId = AnalysisId::fromString($query->getAnalysisId());
        $imageId = ImageId::fromString($query->getImageId());

        return $this->repository->findByRecordId($analysisId, $imageId);
    }
}
