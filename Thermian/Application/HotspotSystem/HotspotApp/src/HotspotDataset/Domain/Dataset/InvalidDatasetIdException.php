<?php
declare(strict_types=1);

namespace Hotspot\HotspotDataset\Domain\Dataset;

use Shared\Domain\InvalidUuidException;
use Throwable;

class InvalidDatasetIdException extends InvalidUuidException
{
    public static function forId(string $id): self
    {
        return new InvalidDatasetIdException("Invalid dataset id {$id}");
    }

    private function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
