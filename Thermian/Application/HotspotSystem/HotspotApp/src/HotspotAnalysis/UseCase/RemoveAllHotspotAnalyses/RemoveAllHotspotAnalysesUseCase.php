<?php
declare(strict_types=1);

namespace Hotspot\HotspotAnalysis\UseCase\RemoveAllHotspotAnalyses;

class RemoveAllHotspotAnalysesUseCase
{
    private RemoveAllHotspotAnalysesTransaction $transaction;

    public function __construct(RemoveAllHotspotAnalysesTransaction $transaction)
    {
        $this->transaction = $transaction;
    }

    public function execute(): void
    {
        $this->transaction->execute();
    }
}
