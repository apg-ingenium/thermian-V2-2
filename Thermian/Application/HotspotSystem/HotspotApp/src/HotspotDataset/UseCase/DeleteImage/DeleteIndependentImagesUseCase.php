<?php
declare(strict_types=1);

namespace Hotspot\HotspotDataset\UseCase\DeleteImage;

use Hotspot\HotspotDataset\Domain\DatasetRepository\DatasetRepository;

class DeleteIndependentImagesUseCase
{
    private DatasetRepository $datasetRepository;

    public function __construct(DatasetRepository $datasetRepository)
    {
        $this->datasetRepository = $datasetRepository;
    }

    public function execute(): void
    {
        $this->datasetRepository->removeIndependentImages();
    }
}
