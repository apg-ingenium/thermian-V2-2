<?php
declare(strict_types=1);

namespace Hotspot\HotspotAnalysis\UseCase\FindHotspotAnalysisSummary;

class FindHotspotAnalysisSummaryQuery
{
    private string $analysisId;

    public function __construct(string $analysisId)
    {
        $this->analysisId = $analysisId;
    }

    public function getAnalysisId(): string
    {
        return $this->analysisId;
    }
}
