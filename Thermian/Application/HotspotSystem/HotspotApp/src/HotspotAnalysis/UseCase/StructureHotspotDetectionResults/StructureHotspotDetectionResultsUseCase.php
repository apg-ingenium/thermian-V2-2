<?php
declare(strict_types=1);

namespace Hotspot\HotspotAnalysis\UseCase\StructureHotspotDetectionResults;

use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\AnalysisId;
use Hotspot\HotspotAnalysis\Domain\HotspotAnalysisCsvParser\HotspotAnalysisCsvParser;
use Hotspot\HotspotAnalysis\Domain\HotspotAnalysisRepository\HotspotAnalysisRepository;
use Hotspot\HotspotResults\UseCase\FindOutputCsv\FindOutputCsvsForAnalysisQuery;
use Hotspot\HotspotResults\UseCase\FindOutputCsv\FindOutputCsvsForAnalysisUseCase;

class StructureHotspotDetectionResultsUseCase
{
    private FindOutputCsvsForAnalysisUseCase $findAnalysisCsvsUseCase;
    private HotspotAnalysisCsvParser $parser;
    private HotspotAnalysisRepository $repository;

    public function __construct(
        FindOutputCsvsForAnalysisUseCase $findAnalysisCsvsUseCase,
        HotspotAnalysisCsvParser $hotspotCsvParser,
        HotspotAnalysisRepository $repository,
    ) {
        $this->findAnalysisCsvsUseCase = $findAnalysisCsvsUseCase;
        $this->parser = $hotspotCsvParser;
        $this->repository = $repository;
    }

    public function execute(StructureHotspotDetectionResultsCommand $command): void
    {
        $analysisId = AnalysisId::fromString($command->getAnalysisId());
        $analysisTarget = $command->getAnalysisTarget();
        $targetImageNames = $command->getTargetImageNames();

        $analysisCsvResults = $this->findAnalysisCsvsUseCase->execute(
            new FindOutputCsvsForAnalysisQuery($analysisId->value())
        );

        $analysis = $this->parser->parseAnalysis($analysisId, $analysisTarget, $targetImageNames, $analysisCsvResults);

        $this->repository->saveAnalysis($analysis);
    }
}
