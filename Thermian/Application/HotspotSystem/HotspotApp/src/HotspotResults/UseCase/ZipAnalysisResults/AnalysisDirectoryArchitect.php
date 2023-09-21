<?php
declare(strict_types=1);

namespace Hotspot\HotspotResults\UseCase\ZipAnalysisResults;

use Generator;

interface AnalysisDirectoryArchitect
{
    /**
     * @param \Generator<\Hotspot\HotspotResults\UseCase\ZipAnalysisResults\AnalysisRecordFile> $analysisFiles
     * @return \Generator<string, string>
     */
    public function structure(Generator $analysisFiles): Generator;
}
