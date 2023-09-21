<?php
declare(strict_types=1);

namespace Hotspot\HotspotDataset\UseCase\DeleteImage;

use Hotspot\HotspotDataset\Domain\DatasetRepository\DatasetRepository;
use Hotspot\HotspotDataset\Domain\Image\ImageId;

class DeleteImageUseCase
{
    private DatasetRepository $datasetRepository;

    public function __construct(DatasetRepository $datasetRepository)
    {
        $this->datasetRepository = $datasetRepository;
    }

    public function execute(DeleteImageCommand $command): void
    {
        $imageId = ImageId::fromString($command->getImageId());
        $this->datasetRepository->removeImageById($imageId);
    }
}
