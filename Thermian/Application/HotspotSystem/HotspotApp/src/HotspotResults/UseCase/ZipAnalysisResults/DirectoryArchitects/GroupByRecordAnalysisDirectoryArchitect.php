<?php
declare(strict_types=1);

namespace Hotspot\HotspotResults\UseCase\ZipAnalysisResults\DirectoryArchitects;

use Generator;
use Hotspot\HotspotResults\UseCase\ZipAnalysisResults\AnalysisDirectoryArchitect;

class GroupByRecordAnalysisDirectoryArchitect implements AnalysisDirectoryArchitect
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

        foreach ($analysisFiles as $result) {
            $directory = $this->removeExtension($result->getRecordName());
            $path = "{$directory}/{$result->getName()}";

            yield $path => $result->getContent();
        }
    }

    private function removeExtension(string $fileName): string
    {
        return explode('.', $fileName, 2)[0];
    }
}
