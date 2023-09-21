<?php
declare(strict_types=1);

namespace Hotspot\HotspotDataset\UseCase\FindImage;

use Hotspot\HotspotDataset\Domain\Image\Image;
use Hotspot\HotspotDataset\Domain\Image\ImageId;
use Hotspot\HotspotDataset\Domain\ImageRepository\ImageRepository;

class FindImageUseCase
{
    private ImageRepository $repository;

    public function __construct(ImageRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(FindImageQuery $query): ?Image
    {
        $imageId = ImageId::fromString($query->getImageId());

        return $this->repository->findById($imageId);
    }
}
