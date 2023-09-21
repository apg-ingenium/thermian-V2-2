<?php
declare(strict_types=1);

namespace Hotspot\HotspotDataset\Domain\File;

use InvalidArgumentException;
use Throwable;

class InvalidFileNameException extends InvalidArgumentException
{
    public static function blank(): self
    {
        return new InvalidFileNameException('The name of a file must not be blank');
    }

    public static function create(): self
    {
        return new InvalidFileNameException('File names must contain between 1 and 64 characters');
    }

    private function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
