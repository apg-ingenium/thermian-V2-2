<?php
declare(strict_types=1);

namespace Hotspot\HotspotResults\UseCase\ZipAnalysisResults\DirectoryArchitects;

use Generator;
use Hotspot\HotspotResults\UseCase\ZipAnalysisResults\AnalysisDirectoryArchitect;

class OneImagePerRecordAnalysisDirectoryArchitect implements AnalysisDirectoryArchitect
{
    /**
     * @param \Generator<\Hotspot\HotspotResults\UseCase\ZipAnalysisResults\AnalysisRecordFile> $analysisFiles
     * @return \Generator<string, string>
     */
    public function structure(Generator $analysisFiles): Generator
    {
        if (!$analysisFiles->valid()) {
            return yield 'no-results.txt' => '';
        }

        foreach ($analysisFiles as $file) {
            yield $file->getRecordName() => $file->getContent();
        }
    }
}
