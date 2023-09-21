<?php
declare(strict_types=1);

namespace Hotspot\HotspotResults\UseCase\FindOutputCsv;

use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\AnalysisId;
use Hotspot\HotspotResults\Domain\OutputCsvRepository\OutputCsvRepository;

class FindOutputCsvsForAnalysisUseCase
{
    private OutputCsvRepository $repository;

    public function __construct(OutputCsvRepository $repository)
    {
        $this->repository = $repository;
    }

    /** @return array<array<\Hotspot\HotspotResults\Domain\HotspotCsv\HotspotCsv>> */
    public function execute(FindOutputCsvsForAnalysisQuery $query): array
    {
        $analysisId = AnalysisId::fromString($query->getAnalysisId());

        return $this->repository->findByAnalysisId($analysisId);
    }
}
