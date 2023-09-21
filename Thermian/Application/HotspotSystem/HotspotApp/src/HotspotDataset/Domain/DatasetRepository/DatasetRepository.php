<?php
declare(strict_types=1);

namespace Hotspot\HotspotDataset\Domain\DatasetRepository;

use Hotspot\HotspotDataset\Domain\Dataset\Dataset;
use Hotspot\HotspotDataset\Domain\Dataset\DatasetStats;
use Hotspot\HotspotDataset\Domain\Image\ImageId;
use Shared\Domain\Uuid;

interface DatasetRepository
{
    public function save(Dataset $dataset): void;

    public function addDatasetImages(Dataset $newImages): void;

    public function containsId(Uuid $datasetId): bool;

    /** @return array<\Hotspot\HotspotDataset\Domain\Dataset\DatasetStats> */
    public function findAllDatasetStats(): array;

    public function findDatasetStatsById(Uuid $datasetId): ?DatasetStats;

    /** @return array<\Hotspot\HotspotDataset\Domain\Image\ImageId> */
    public function findIndependentImageIds(): array;

    public function removeById(Uuid $datasetId): void;

    public function removeImageById(ImageId $imageId): void;

    public function removeAll(): void;

    public function removeIndependentImages(): void;
}
