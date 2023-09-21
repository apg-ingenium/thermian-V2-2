<?php
declare(strict_types=1);

namespace Hotspot\HotspotDataset\UseCase\ZipDataset;

class ZipDatasetCommand
{
    private string $datasetId;
    private string $zipPath;

    public function __construct(string $datasetId, string $zipPath)
    {
        $this->datasetId = $datasetId;
        $this->zipPath = $zipPath;
    }

    public function getDatasetId(): string
    {
        return $this->datasetId;
    }

    public function getOutputZipPath(): string
    {
        return $this->zipPath;
    }
}
