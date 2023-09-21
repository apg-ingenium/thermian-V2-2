<?php
declare(strict_types=1);

namespace Hotspot\HotspotAnalysis\UseCase\RemoveAllHotspotAnalyses;

interface RemoveAllHotspotAnalysesTransaction
{
    public function execute(): void;
}
