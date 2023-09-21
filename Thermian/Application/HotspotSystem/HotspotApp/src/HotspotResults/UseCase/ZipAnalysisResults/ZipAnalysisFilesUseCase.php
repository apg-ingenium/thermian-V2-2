<?php
declare(strict_types=1);

namespace Hotspot\HotspotResults\UseCase\ZipAnalysisResults;

use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\AnalysisId;
use Shared\Utils\Zipper;

class ZipAnalysisFilesUseCase
{
    private AnalysisFileFinder $analysisFileFinder;
    private AnalysisDirectoryArchitect $directoryArchitect;
    private Zipper $zipper;

    public function __construct(
        AnalysisFileFinder $analysisFileFinder,
        AnalysisDirectoryArchitect $directoryArchitect,
        Zipper $zipper
    ) {
        $this->analysisFileFinder = $analysisFileFinder;
        $this->directoryArchitect = $directoryArchitect;
        $this->zipper = $zipper;
    }

    public function execute(ZipAnalysisFilesCommand $command): void
    {
        $analysisId = AnalysisId::fromString($command->getAnalysisId());
        $pathToOutputZip = $command->getOutputZipPath();

        $analysisFiles = $this->analysisFileFinder->find($analysisId);

        if (!$analysisFiles->valid()) {
            throw AnalysisFilesNotFoundException::withId(
                $command->getAnalysisId()
            );
        }

        $structuredAnalysisFiles = $this->directoryArchitect->structure($analysisFiles);
        $this->zipper->zip($pathToOutputZip, $structuredAnalysisFiles);
    }
}
