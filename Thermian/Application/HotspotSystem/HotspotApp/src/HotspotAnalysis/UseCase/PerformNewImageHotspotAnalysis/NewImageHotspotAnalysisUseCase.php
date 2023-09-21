<?php
declare(strict_types=1);

namespace Hotspot\HotspotAnalysis\UseCase\PerformNewImageHotspotAnalysis;

use Hotspot\HotspotAnalysis\UseCase\StructureHotspotDetectionResults\StructureHotspotDetectionResultsCommand;
use Hotspot\HotspotAnalysis\UseCase\StructureHotspotDetectionResults\StructureHotspotDetectionResultsUseCase;
use Hotspot\HotspotDataset\UseCase\StoreImage\StoreImageCommand;
use Hotspot\HotspotDataset\UseCase\StoreImage\StoreImageUseCase;
use Hotspot\HotspotDetection\UseCase\ExecuteImageHotspotDetection\ExecuteImageHotspotDetectionCommand;
use Hotspot\HotspotDetection\UseCase\ExecuteImageHotspotDetection\ExecuteImageHotspotDetectionUseCase;

class NewImageHotspotAnalysisUseCase
{
    private StoreImageUseCase $storeImageUseCase;
    private ExecuteImageHotspotDetectionUseCase $hotspotDetectionUseCase;
    private StructureHotspotDetectionResultsUseCase $createStructuredDetectionResultsUseCase;

    public function __construct(
        StoreImageUseCase $storeImageUseCase,
        ExecuteImageHotspotDetectionUseCase $executeHotspotDetectionUseCase,
        StructureHotspotDetectionResultsUseCase $createStructuredDetectionResultsUseCase
    ) {
        $this->storeImageUseCase = $storeImageUseCase;
        $this->hotspotDetectionUseCase = $executeHotspotDetectionUseCase;
        $this->createStructuredDetectionResultsUseCase = $createStructuredDetectionResultsUseCase;
    }

    public function execute(NewImageHotspotAnalysisCommand $command): void
    {
        $this->storeImageUseCase->execute(
            new StoreImageCommand(
                $command->getImageId(),
                $command->getImageName(),
                $command->getImagePath()
            )
        );

        $this->hotspotDetectionUseCase->execute(
            new ExecuteImageHotspotDetectionCommand(
                $command->getAnalysisId(),
                $command->getImageId()
            )
        );

        $this->createStructuredDetectionResultsUseCase->execute(
            new StructureHotspotDetectionResultsCommand(
                $command->getAnalysisId(),
                $command->getImageName(),
                [$command->getImageId() => $command->getImageName()]
            )
        );
    }
}
