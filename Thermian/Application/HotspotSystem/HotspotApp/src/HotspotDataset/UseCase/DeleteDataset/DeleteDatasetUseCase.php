<?php
declare(strict_types=1);

namespace Hotspot\HotspotDataset\UseCase\DeleteDataset;

use Hotspot\HotspotDataset\Domain\Dataset\DatasetId;
use Hotspot\HotspotDataset\Domain\DatasetRepository\DatasetRepository;

class DeleteDatasetUseCase
{
    private DatasetRepository $repository;

    public function __construct(DatasetRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(DeleteDatasetCommand $command): void
    {
        $datasetId = DatasetId::fromString($command->getDatasetId());
        $this->repository->removeById($datasetId);
    }
}
