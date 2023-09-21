<?php
declare(strict_types=1);

namespace Hotspot\HotspotDetection\UseCase\ExecuteDatasetHotspotDetection;

use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\AnalysisId;
use Hotspot\HotspotDataset\Domain\Dataset\DatasetId;
use Hotspot\HotspotDetection\Domain\HotspotAnalyzer;

class ExecuteDatasetHotspotDetectionUseCase
{
    private HotspotAnalyzer $hotspotDetector;

    public function __construct(HotspotAnalyzer $hotspotDetector)
    {
        $this->hotspotDetector = $hotspotDetector;
    }

    public function execute(DatasetHotspotDetectionCommand $command): void
    {
        $analysisId = AnalysisId::fromString($command->getAnalysisId());
        $datasetId = DatasetId::fromString($command->getDatasetId());
        $this->hotspotDetector->analyzeDataset($analysisId, $datasetId);
    }
}
