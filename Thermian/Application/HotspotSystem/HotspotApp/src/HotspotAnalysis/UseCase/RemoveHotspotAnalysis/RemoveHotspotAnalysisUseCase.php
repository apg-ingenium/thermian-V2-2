<?php
declare(strict_types=1);

namespace Hotspot\HotspotAnalysis\UseCase\RemoveHotspotAnalysis;

use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\AnalysisId;
use Hotspot\HotspotAnalysis\Domain\HotspotAnalysisRepository\HotspotAnalysisRepository;
use Hotspot\HotspotResults\Domain\HotspotCsvRepository\HotspotCsvRepository;
use Hotspot\HotspotResults\Domain\HotspotImageRepository\HotspotImageRepository;

class RemoveHotspotAnalysisUseCase
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

    public function execute(RemoveHotspotAnalysisCommand $query): void
    {
        $analysisId = AnalysisId::fromString($query->getAnalysisId());

        $this->hotspotAnalysisRepository->removeAnalysisById($analysisId);
        $this->hotspotImageRepository->removeByAnalysisId($analysisId);
        $this->hotspotCsvRepository->removeByAnalysisId($analysisId);
    }
}
