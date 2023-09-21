<?php
declare(strict_types=1);

namespace Hotspot\HotspotDataset\UseCase\AddDatasetImages;

class AddDatasetImagesCommand
{
    private string $datasetId;

    /** @var array<string> */
    private array $imageNames;

    /** @var array<string> */
    private array $imagePaths;

    /** @var ?array<string> */
    private ?array $imageIds;

    /**
     * @param string $datasetId
     * @param array<string> $imageNames
     * @param array<string> $imagePaths
     * @param array<string> $imageIds
     */
    public function __construct(string $datasetId, array $imageNames, array $imagePaths, ?array $imageIds = null)
    {
        $this->datasetId = $datasetId;
        $this->imageNames = $imageNames;
        $this->imagePaths = $imagePaths;
        $this->imageIds = $imageIds;
    }

    public function getDatasetId(): string
    {
        return $this->datasetId;
    }

    /** @return array<string> */
    public function getImageNames(): array
    {
        return $this->imageNames;
    }

    /** @return array<string> */
    public function getImagePaths(): array
    {
        return $this->imagePaths;
    }

    /** @return ?array<string> */
    public function getImageIds(): ?array
    {
        return $this->imageIds;
    }
}
