<?php
declare(strict_types=1);

namespace Hotspot\HotspotDataset\UseCase\FindDatasetStats;

class FindDatasetStatsQuery
{
    private string $datasetId;

    public function __construct(string $datasetId)
    {
        $this->datasetId = $datasetId;
    }

    public function getDatasetId(): string
    {
        return $this->datasetId;
    }
}
