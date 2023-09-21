<?php
declare(strict_types=1);

namespace Hotspot\HotspotDataset\UseCase\StoreImage;

use Hotspot\HotspotDataset\Domain\Image\Image;
use Hotspot\HotspotDataset\Domain\Image\ImageId;
use Hotspot\HotspotDataset\Domain\ImageRepository\ImageRepository;

class StoreImageUseCase
{
    private ImageRepository $repository;

    public function __construct(ImageRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(StoreImageCommand $command): void
    {
        $imageId = ImageId::fromString($command->getId());
        $inputImage = Image::fromPath($imageId, $command->getPath(), $command->getName());
        $this->repository->save($inputImage);
    }
}
