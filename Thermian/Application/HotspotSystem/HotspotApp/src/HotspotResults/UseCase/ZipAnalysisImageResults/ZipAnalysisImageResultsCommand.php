<?php
declare(strict_types=1);

namespace Hotspot\HotspotResults\UseCase\ZipAnalysisImageResults;

class ZipAnalysisImageResultsCommand
{
    private string $analysisId;
    private string $outputZipPath;

    public function __construct(string $analysisId, string $outputZipPath)
    {
        $this->analysisId = $analysisId;
        $this->outputZipPath = $outputZipPath;
    }

    public function getAnalysisId(): string
    {
        return $this->analysisId;
    }

    public function getOutputZipPath(): string
    {
        return $this->outputZipPath;
    }
}
