<?php
declare(strict_types=1);

namespace Hotspot\HotspotDataset\Domain\ImageRepository;

use Hotspot\HotspotDataset\Domain\Image\ImageId;
use Shared\Persistence\DuplicateIdException;

class DuplicateImageIdException extends DuplicateIdException
{
    public static function forId(ImageId $imageId): self
    {
        return new DuplicateImageIdException($imageId);
    }

    private function __construct(ImageId $imageId)
    {
        parent::__construct("Duplicate image id {$imageId->value()}");
    }
}
