<?php
declare(strict_types=1);

namespace Hotspot\HotspotAnalysis\UseCase\PerformExistingDatasetHotspotAnalysis;

use Hotspot\HotspotAnalysis\UseCase\StructureHotspotDetectionResults\StructureHotspotDetectionResultsCommand;
use Hotspot\HotspotAnalysis\UseCase\StructureHotspotDetectionResults\StructureHotspotDetectionResultsUseCase;
use Hotspot\HotspotDataset\Domain\Dataset\DatasetId;
use Hotspot\HotspotDataset\Domain\DatasetRepository\DatasetRepository;
use Hotspot\HotspotDataset\Domain\ImageRepository\ImageRepository;
use Hotspot\HotspotDetection\UseCase\ExecuteDatasetHotspotDetection\DatasetHotspotDetectionCommand;
use Hotspot\HotspotDetection\UseCase\ExecuteDatasetHotspotDetection\ExecuteDatasetHotspotDetectionUseCase;

class ExistingDatasetHotspotAnalysisUseCase
{
    private ExecuteDatasetHotspotDetectionUseCase $hotspotDetectionUseCase;
    private StructureHotspotDetectionResultsUseCase $structureDetectionResultsUseCase;
    private DatasetRepository $datasetRepository;
    private ImageRepository $imageRepository;

    public function __construct(
        ExecuteDatasetHotspotDetectionUseCase $executeHotspotDetectionUseCase,
        StructureHotspotDetectionResultsUseCase $structureHotspotDetectionResultsUseCase,
        DatasetRepository $datasetRepository,
        ImageRepository $imageRepository
    ) {
        $this->hotspotDetectionUseCase = $executeHotspotDetectionUseCase;
        $this->structureDetectionResultsUseCase = $structureHotspotDetectionResultsUseCase;
        $this->datasetRepository = $datasetRepository;
        $this->imageRepository = $imageRepository;
    }

    public function execute(ExistingDatasetHotspotAnalysisCommand $command): void
    {
        $this->hotspotDetectionUseCase->execute(
            new DatasetHotspotDetectionCommand(
                $command->getAnalysisId(),
                $command->getDatasetId()
            )
        );

        $datasetId = DatasetId::fromString($command->getDatasetId());
        $datasetStats = $this->datasetRepository->findDatasetStatsById($datasetId);

        assert(!is_null($datasetStats));

        $imageIds = $datasetStats->getImageIds();
        $datasetNames = $this->imageRepository->findImageNames($imageIds);

        $this->structureDetectionResultsUseCase->execute(
            new StructureHotspotDetectionResultsCommand(
                $command->getAnalysisId(),
                $datasetStats->getName()->value(),
                $datasetNames,
            )
        );
    }
}
