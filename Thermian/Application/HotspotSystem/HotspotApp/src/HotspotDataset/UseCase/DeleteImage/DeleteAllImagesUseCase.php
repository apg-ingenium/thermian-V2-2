<?php
declare(strict_types=1);

namespace Hotspot\HotspotDataset\UseCase\DeleteImage;

use Hotspot\HotspotDataset\Domain\DatasetRepository\DatasetRepository;
use Hotspot\HotspotDataset\Domain\ImageRepository\ImageRepository;

class DeleteAllImagesUseCase
{
    private ImageRepository $imageRepository;
    private DatasetRepository $datasetRepository;

    public function __construct(ImageRepository $imageRepository, DatasetRepository $datasetRepository)
    {
        $this->imageRepository = $imageRepository;
        $this->datasetRepository = $datasetRepository;
    }

    public function execute(): void
    {
        $this->datasetRepository->removeAll();
        $this->imageRepository->removeAll();
    }
}
