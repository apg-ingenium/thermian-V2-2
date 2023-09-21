<?php
declare(strict_types=1);

namespace Hotspot\HotspotDataset\Domain\DatasetRepository;

use RuntimeException;
use Throwable;

class DatasetNotFoundException extends RuntimeException
{
    public static function create(): self
    {
        return new DatasetNotFoundException('Dataset not found');
    }

    public static function withId(string $id): self
    {
        return new DatasetNotFoundException("Dataset not found with id {$id}");
    }

    private function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
