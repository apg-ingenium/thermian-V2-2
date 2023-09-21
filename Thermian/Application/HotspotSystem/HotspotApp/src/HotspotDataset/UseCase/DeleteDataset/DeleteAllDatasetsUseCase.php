<?php
declare(strict_types=1);

namespace Hotspot\HotspotDataset\UseCase\DeleteDataset;

use Hotspot\HotspotDataset\Domain\DatasetRepository\DatasetRepository;

class DeleteAllDatasetsUseCase
{
    private DatasetRepository $repository;

    public function __construct(DatasetRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(): void
    {
        $this->repository->removeAll();
    }
}
