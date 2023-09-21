<?php
declare(strict_types=1);

namespace Hotspot\HotspotDataset\Domain\Dataset;

use Shared\Domain\InvalidUuidException;
use Shared\Domain\Uuid;

class DatasetId extends Uuid
{
    public static function fromString(string $value): static
    {
        try {
            return parent::fromString($value);
        }
        catch (InvalidUuidException) {
            throw InvalidDatasetIdException::forId($value);
        }
    }
}
