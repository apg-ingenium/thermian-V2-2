<?php
declare(strict_types=1);

namespace Hotspot\HotspotDataset\UseCase\FindImage;

class FindImageQuery
{
    private string $imageId;

    public function __construct(string $imageId)
    {
        $this->imageId = $imageId;
    }

    public function getImageId(): string
    {
        return $this->imageId;
    }
}
