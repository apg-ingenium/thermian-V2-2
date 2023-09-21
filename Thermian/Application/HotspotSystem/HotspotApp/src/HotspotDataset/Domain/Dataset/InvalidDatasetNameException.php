<?php
declare(strict_types=1);

namespace Hotspot\HotspotDataset\Domain\Dataset;

use InvalidArgumentException;
use Throwable;

class InvalidDatasetNameException extends InvalidArgumentException
{
    public static function create(): self
    {
        return new InvalidDatasetNameException(
            'Dataset names must contain between 1 and 64 characters'
        );
    }

    private function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
