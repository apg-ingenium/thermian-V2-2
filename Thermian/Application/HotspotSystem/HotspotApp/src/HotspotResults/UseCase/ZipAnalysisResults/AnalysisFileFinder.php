<?php
declare(strict_types=1);

namespace Hotspot\HotspotResults\UseCase\ZipAnalysisResults;

use Generator;
use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\AnalysisId;

interface AnalysisFileFinder
{
    /** @return \Generator<\Hotspot\HotspotResults\UseCase\ZipAnalysisResults\AnalysisRecordFile> */
    public function find(AnalysisId $analysisId): Generator;
}
