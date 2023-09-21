<?php
declare(strict_types=1);

namespace Hotspot\HotspotAnalysis\Domain\HotspotAnalysisRepository;

use Hotspot\HotspotAnalysis\Domain\Hotspot\HotspotId;
use Shared\Persistence\DuplicateIdException;
use Throwable;

class DuplicateHotspotIdException extends DuplicateIdException
{
    public static function create(): self
    {
        return new DuplicateHotspotIdException('Duplicate hotspot id');
    }

    public static function forId(HotspotId $id): self
    {
        return new DuplicateHotspotIdException("Duplicate hotspot Id {$id->value()}");
    }

    private function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
