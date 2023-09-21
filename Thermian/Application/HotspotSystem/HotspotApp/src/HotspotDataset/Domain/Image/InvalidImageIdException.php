<?php
declare(strict_types=1);

namespace Hotspot\HotspotDataset\Domain\Image;

use Shared\Domain\InvalidUuidException;
use Throwable;

class InvalidImageIdException extends InvalidUuidException
{
    public static function create(): self
    {
        return new InvalidImageIdException('Invalid image id');
    }

    public static function forId(string $id): self
    {
        return new InvalidImageIdException("Invalid image id {$id}");
    }

    private function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
