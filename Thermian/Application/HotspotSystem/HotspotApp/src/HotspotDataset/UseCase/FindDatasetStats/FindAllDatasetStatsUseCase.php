<?php
declare(strict_types=1);

namespace Hotspot\HotspotDataset\UseCase\FindDatasetStats;

use Hotspot\HotspotDataset\Domain\DatasetRepository\DatasetRepository;
use Hotspot\HotspotDataset\Domain\Image\ImageId;

class FindAllDatasetStatsUseCase
{
    private DatasetRepository $repository;

    public function __construct(DatasetRepository $repository)
    {
        $this->repository = $repository;
    }

    /** @return array<array<mixed>> */
    public function execute(): array
    {
        $datasetStats = $this->repository->findAllDatasetStats();

        $datasets = [];
        foreach ($datasetStats as $dataset) {
            $datasets[] = [
                'id' => $dataset->getId()->value(),
                'name' => $dataset->getName()->value(),
                'date' => $dataset->getCreationDate()->format('Y/m/d H:i:s'),
                'numImages' => $dataset->getNumImages(),
                'size' => $dataset->getSize(),
                'imageIds' => array_map(fn(ImageId $imageId) => $imageId->value(), $dataset->getImageIds()),
            ];
        }

        return $datasets;
    }
}
