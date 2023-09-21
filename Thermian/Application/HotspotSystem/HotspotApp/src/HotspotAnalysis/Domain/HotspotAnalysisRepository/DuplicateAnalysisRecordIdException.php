<?php
declare(strict_types=1);

namespace Hotspot\HotspotAnalysis\Domain\HotspotAnalysisRepository;

use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\AnalysisId;
use Hotspot\HotspotDataset\Domain\Image\ImageId;
use Shared\Persistence\DuplicateIdException;

class DuplicateAnalysisRecordIdException extends DuplicateIdException
{
    public static function create(AnalysisId $analysisId, ImageId $imageId): self
    {
        return new DuplicateAnalysisRecordIdException($analysisId, $imageId);
    }

    private function __construct(AnalysisId $analysisId, ImageId $imageId)
    {
        parent::__construct(
            "Duplicate record for analysis {$analysisId->value()} and image {$imageId->value()}"
        );
    }
}
