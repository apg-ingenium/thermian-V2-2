<?php
declare(strict_types=1);

namespace Hotspot\HotspotAnalysis\Domain\HotspotAnalysisRepository;

use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\AnalysisId;
use Shared\Persistence\DuplicateIdException;

class DuplicateAnalysisIdException extends DuplicateIdException
{
    public static function forId(AnalysisId $id): self
    {
        return new DuplicateAnalysisIdException($id);
    }

    private function __construct(AnalysisId $id)
    {
        parent::__construct("Duplicate analysis Id {$id->value()}");
    }
}
