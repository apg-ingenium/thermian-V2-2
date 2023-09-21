<?php
declare(strict_types=1);

namespace Hotspot\HotspotAnalysis\Domain\HotspotAnalysis;

use Shared\Domain\InvalidUuidException;
use Throwable;

class InvalidAnalysisIdException extends InvalidUuidException
{
    public static function create(): self
    {
        return new InvalidAnalysisIdException('Invalid analysis id');
    }

    public static function forId(string $id): self
    {
        return new InvalidAnalysisIdException("Invalid analysis id {$id}");
    }

    private function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
