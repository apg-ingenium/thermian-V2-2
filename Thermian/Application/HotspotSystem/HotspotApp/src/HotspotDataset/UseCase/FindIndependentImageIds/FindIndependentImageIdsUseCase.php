<?php
declare(strict_types=1);

namespace Hotspot\HotspotDataset\UseCase\FindIndependentImageIds;

use Hotspot\HotspotDataset\Domain\DatasetRepository\DatasetRepository;
use Hotspot\HotspotDataset\Domain\Image\ImageId;

class FindIndependentImageIdsUseCase
{
    private DatasetRepository $repository;

    public function __construct(DatasetRepository $repository)
    {
        $this->repository = $repository;
    }

    /** @return array<string> */
    public function execute(): array
    {
        $ids = $this->repository->findIndependentImageIds();

        return array_map(fn(ImageId $id) => $id->value(), $ids);
    }
}
