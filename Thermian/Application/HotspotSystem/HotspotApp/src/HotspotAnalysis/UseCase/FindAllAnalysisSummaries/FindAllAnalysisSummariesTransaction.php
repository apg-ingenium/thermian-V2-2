<?php
declare(strict_types=1);

namespace Hotspot\HotspotAnalysis\UseCase\FindAllAnalysisSummaries;

interface FindAllAnalysisSummariesTransaction
{
    /** @return array<array<string, mixed>> */
    public function execute(): array;
}
