<?php
declare(strict_types=1);

namespace Hotspot\HotspotDataset\UseCase\DeleteDataset;

class DeleteDatasetCommand
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
