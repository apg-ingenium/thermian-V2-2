<?php
declare(strict_types=1);

namespace Hotspot\HotspotAnalysis\UseCase\FindAllAnalysisSummaries;

class FindAllHotspotAnalysisSummariesUseCase
{
    private FindAllAnalysisSummariesTransaction $transaction;

    public function __construct(FindAllAnalysisSummariesTransaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /** @return array<array<string, mixed>> */
    public function execute(): array
    {
        return $this->transaction->execute();
    }
}
