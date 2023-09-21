<?php
declare(strict_types=1);

namespace Hotspot\HotspotAnalysis\UseCase\StructureHotspotDetectionResults;

class StructureHotspotDetectionResultsCommand
{
    private string $analysisId;
    private string $analysisTarget;

    /** @var array<string, string> */
    private array $targetImageNames;

    /** @param array<string, string> $targetImageNames */
    public function __construct(string $analysisId, string $analysisTarget, array $targetImageNames)
    {
        $this->analysisId = $analysisId;
        $this->analysisTarget = $analysisTarget;
        $this->targetImageNames = $targetImageNames;
    }

    public function getAnalysisId(): string
    {
        return $this->analysisId;
    }

    public function getAnalysisTarget(): string
    {
        return $this->analysisTarget;
    }

    /** @return array<string, string> */
    public function getTargetImageNames(): array
    {
        return $this->targetImageNames;
    }
}
