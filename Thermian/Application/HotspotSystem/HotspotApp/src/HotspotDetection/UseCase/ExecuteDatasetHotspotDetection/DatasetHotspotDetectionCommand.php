<?php
declare(strict_types=1);

namespace Hotspot\HotspotDetection\UseCase\ExecuteDatasetHotspotDetection;

class DatasetHotspotDetectionCommand
{
    private string $analysisId;
    private string $datasetId;

    public function __construct(string $analysisId, string $datasetId)
    {
        $this->analysisId = $analysisId;
        $this->datasetId = $datasetId;
    }

    public function getAnalysisId(): string
    {
        return $this->analysisId;
    }

    public function getDatasetId(): string
    {
        return $this->datasetId;
    }
}
