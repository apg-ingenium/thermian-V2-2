<?php
declare(strict_types=1);

namespace Hotspot\HotspotResults\UseCase\ZipAnalysisImageResults;

use Hotspot\HotspotResults\UseCase\ZipAnalysisResults\ZipAnalysisFilesCommand;
use Hotspot\HotspotResults\UseCase\ZipAnalysisResults\ZipAnalysisFilesUseCase;

class ZipAnalysisImageResultsUseCase
{
    private ZipAnalysisFilesUseCase $useCase;

    public function __construct(ZipAnalysisFilesUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function execute(ZipAnalysisImageResultsCommand $command): void
    {
        $this->useCase->execute(
            new ZipAnalysisFilesCommand(
                $command->getAnalysisId(),
                $command->getOutputZipPath()
            )
        );
    }
}
