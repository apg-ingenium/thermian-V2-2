<?php
declare(strict_types=1);

namespace Hotspot\HotspotAnalysis\UseCase\PerformNewDatasetHotspotAnalysis;

use Hotspot\HotspotAnalysis\UseCase\StructureHotspotDetectionResults\StructureHotspotDetectionResultsCommand;
use Hotspot\HotspotAnalysis\UseCase\StructureHotspotDetectionResults\StructureHotspotDetectionResultsUseCase;
use Hotspot\HotspotDataset\Domain\Image\ImageId;
use Hotspot\HotspotDataset\UseCase\StoreDataset\StoreDatasetCommand;
use Hotspot\HotspotDataset\UseCase\StoreDataset\StoreDatasetUseCase;
use Hotspot\HotspotDetection\UseCase\ExecuteDatasetHotspotDetection\DatasetHotspotDetectionCommand;
use Hotspot\HotspotDetection\UseCase\ExecuteDatasetHotspotDetection\ExecuteDatasetHotspotDetectionUseCase;

class NewDatasetHotspotAnalysisUseCase
{
    private StoreDatasetUseCase $storeDatasetUseCase;
    private ExecuteDatasetHotspotDetectionUseCase $hotspotDetectionUseCase;
    private StructureHotspotDetectionResultsUseCase $structureDetectionResultsUseCase;

    public function __construct(
        StoreDatasetUseCase $storeDatasetUseCase,
        ExecuteDatasetHotspotDetectionUseCase $executeHotspotDetectionUseCase,
        StructureHotspotDetectionResultsUseCase $structureHotspotDetectionResultsUseCase
    ) {
        $this->storeDatasetUseCase = $storeDatasetUseCase;
        $this->hotspotDetectionUseCase = $executeHotspotDetectionUseCase;
        $this->structureDetectionResultsUseCase = $structureHotspotDetectionResultsUseCase;
    }

    public function execute(NewDatasetHotspotAnalysisCommand $command): void
    {
        $indexedImageNames = [];
        $imageIds = [];

        foreach ($command->getImageNames() as $imageName) {
            $imageId = ImageId::random()->value();
            $indexedImageNames[$imageId] = $imageName;
            $imageIds[] = $imageId;
        }

        $this->storeDatasetUseCase->execute(
            new StoreDatasetCommand(
                $command->getDatasetId(),
                $command->getDatasetName(),
                $command->getImageNames(),
                $command->getImagePaths(),
                $imageIds,
            )
        );

        $this->hotspotDetectionUseCase->execute(
            new DatasetHotspotDetectionCommand(
                $command->getAnalysisId(),
                $command->getDatasetId()
            )
        );

        $this->structureDetectionResultsUseCase->execute(
            new StructureHotspotDetectionResultsCommand(
                $command->getAnalysisId(),
                $command->getDatasetName(),
                $indexedImageNames
            )
        );
    }
}
