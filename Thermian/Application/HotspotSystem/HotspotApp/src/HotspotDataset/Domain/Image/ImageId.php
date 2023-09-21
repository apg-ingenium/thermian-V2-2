<?php
declare(strict_types=1);

namespace Hotspot\HotspotDataset\Domain\Image;

use Shared\Domain\InvalidUuidException;
use Shared\Domain\Uuid;

class ImageId extends Uuid
{
    public static function fromString(string $value): static
    {
        try {
            return parent::fromString($value);
        }
        catch (InvalidUuidException) {
            throw InvalidImageIdException::forId($value);
        }
    }
}
