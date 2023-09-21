<?php
declare(strict_types=1);

namespace Hotspot\HotspotDataset\UseCase\FindDatasetStats;

use Hotspot\HotspotDataset\Domain\Dataset\DatasetId;
use Hotspot\HotspotDataset\Domain\DatasetRepository\DatasetRepository;
use Hotspot\HotspotDataset\Domain\Image\ImageId;

class FindDatasetStatsUseCase
{
    private DatasetRepository $repository;

    public function __construct(DatasetRepository $repository)
    {
        $this->repository = $repository;
    }

    /** @return array<string, mixed> */
    public function execute(FindDatasetStatsQuery $query): ?array
    {
        $datasetId = DatasetId::fromString($query->getDatasetId());
        $dataset = $this->repository->findDatasetStatsById($datasetId);

        if (is_null($dataset)) {
            return null;
        }

        return [
            'id' => $dataset->getId()->value(),
            'name' => $dataset->getName()->value(),
            'date' => $dataset->getCreationDate()->format('Y/m/d H:i:s'),
            'numImages' => $dataset->getNumImages(),
            'size' => $dataset->getSize(),
            'imageIds' => array_map(fn(ImageId $imageId) => $imageId->value(), $dataset->getImageIds()),
        ];
    }
}
