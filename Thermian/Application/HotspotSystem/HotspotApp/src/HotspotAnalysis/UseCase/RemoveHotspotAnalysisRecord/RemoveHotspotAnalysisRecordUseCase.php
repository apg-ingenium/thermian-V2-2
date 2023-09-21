<?php
declare(strict_types=1);

namespace Hotspot\HotspotAnalysis\UseCase\RemoveHotspotAnalysisRecord;

use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\AnalysisId;
use Hotspot\HotspotAnalysis\Domain\HotspotAnalysisRepository\HotspotAnalysisRepository;
use Hotspot\HotspotDataset\Domain\Image\ImageId;
use Hotspot\HotspotResults\Domain\HotspotCsvRepository\HotspotCsvRepository;
use Hotspot\HotspotResults\Domain\HotspotImageRepository\HotspotImageRepository;

class RemoveHotspotAnalysisRecordUseCase
{
    private HotspotAnalysisRepository $hotspotAnalysisRepository;
    private HotspotCsvRepository $hotspotCsvRepository;
    private HotspotImageRepository $hotspotImageRepository;

    public function __construct(
        HotspotAnalysisRepository $hotspotAnalysisRepository,
        HotspotCsvRepository $hotspotCsvRepository,
        HotspotImageRepository $hotspotImageRepository
    ) {
        $this->hotspotAnalysisRepository = $hotspotAnalysisRepository;
        $this->hotspotCsvRepository = $hotspotCsvRepository;
        $this->hotspotImageRepository = $hotspotImageRepository;
    }

    public function execute(RemoveHotspotAnalysisRecordCommand $query): void
    {
        $analysisId = AnalysisId::fromString($query->getAnalysisId());
        $imageId = ImageId::fromString($query->getImageId());

        $this->hotspotAnalysisRepository->removeAnalysisRecordById($analysisId, $imageId);
        $this->hotspotImageRepository->removeByRecordId($analysisId, $imageId);
        $this->hotspotCsvRepository->removeByRecordId($analysisId, $imageId);
    }
}
