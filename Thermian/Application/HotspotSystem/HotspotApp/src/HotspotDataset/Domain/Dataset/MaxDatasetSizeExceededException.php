<?php
declare(strict_types=1);

namespace Hotspot\HotspotDataset\Domain\Dataset;

use RuntimeException;
use Throwable;

class MaxDatasetSizeExceededException extends RuntimeException
{
    public static function create(): self
    {
        return new MaxDatasetSizeExceededException(
            'Maximum dataset size exceeded'
        );
    }

    private function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
