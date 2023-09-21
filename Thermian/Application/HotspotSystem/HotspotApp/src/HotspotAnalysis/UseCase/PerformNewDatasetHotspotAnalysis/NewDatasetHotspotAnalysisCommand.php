<?php
declare(strict_types=1);

namespace Hotspot\HotspotAnalysis\UseCase\PerformNewDatasetHotspotAnalysis;

class NewDatasetHotspotAnalysisCommand
{
    private string $analysisId;
    private string $datasetId;
    private string $datasetName;

    /** @var array<string> $imagePaths */
    private array $imagePaths;

    /** @var array<string> $imageNames */
    private array $imageNames;

    /**
     * @param string $analysisId
     * @param string $datasetName
     * @param string $datasetId
     * @param array<string> $imageNames
     * @param array<string> $imagePaths
     */
    public function __construct(string $analysisId, string $datasetId, string $datasetName, array $imageNames, array $imagePaths)
    {
        $this->analysisId = $analysisId;
        $this->datasetId = $datasetId;
        $this->datasetName = $datasetName;
        $this->imagePaths = $imagePaths;
        $this->imageNames = $imageNames;
    }

    public function getAnalysisId(): string
    {
        return $this->analysisId;
    }

    public function getDatasetId(): string
    {
        return $this->datasetId;
    }

    public function getDatasetName(): string
    {
        return $this->datasetName;
    }

    /** @return array<string> */
    public function getImagePaths(): array
    {
        return $this->imagePaths;
    }

    /** @return array<string> */
    public function getImageNames(): array
    {
        return $this->imageNames;
    }
}
