<?php
declare(strict_types=1);

namespace Hotspot\HotspotDataset\UseCase\StoreDataset;

use Hotspot\HotspotDataset\Domain\Dataset\DatasetId;
use Hotspot\HotspotDataset\Domain\Dataset\DatasetName;
use Hotspot\HotspotDataset\Domain\Dataset\FileSystemDataset;
use Hotspot\HotspotDataset\Domain\DatasetRepository\DatasetRepository;
use Hotspot\HotspotDataset\Domain\Image\ImageId;

class StoreDatasetUseCase
{
    private DatasetRepository $datasetRepository;

    public function __construct(DatasetRepository $datasetRepository)
    {
        $this->datasetRepository = $datasetRepository;
    }

    public function execute(StoreDatasetCommand $command): void
    {
        $imageIds = $command->getImageIds();
        $imageIds = $imageIds ? array_map(fn($id) => ImageId::fromString($id), $imageIds) : null;

        $dataset = FileSystemDataset::create(
            DatasetId::fromString($command->getDatasetId()),
            DatasetName::create($command->getDatasetName()),
            $command->getImagePaths(),
            $command->getImageNames(),
            $imageIds
        );

        $this->datasetRepository->save($dataset);
    }
}
