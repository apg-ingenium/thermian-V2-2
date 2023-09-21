<?php
declare(strict_types=1);

namespace Hotspot\HotspotAnalysis\Domain\HotspotAnalysis;

use Shared\Domain\InvalidUuidException;
use Shared\Domain\Uuid;

class AnalysisId extends Uuid
{
    public static function fromString(string $value): static
    {
        try {
            return parent::fromString($value);
        }
        catch (InvalidUuidException) {
            throw InvalidAnalysisIdException::forId($value);
        }
    }
}
