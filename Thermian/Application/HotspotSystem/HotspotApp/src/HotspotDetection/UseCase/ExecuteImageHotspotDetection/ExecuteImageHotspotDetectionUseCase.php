<?php
declare(strict_types=1);

namespace Hotspot\HotspotDetection\UseCase\ExecuteImageHotspotDetection;

use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\AnalysisId;
use Hotspot\HotspotDataset\Domain\Image\ImageId;
use Hotspot\HotspotDetection\Domain\HotspotAnalyzer;

class ExecuteImageHotspotDetectionUseCase
{
    private HotspotAnalyzer $hotspotDetector;

    public function __construct(HotspotAnalyzer $hotspotDetector)
    {
        $this->hotspotDetector = $hotspotDetector;
    }

    public function execute(ExecuteImageHotspotDetectionCommand $command): void
    {
        $analysisId = AnalysisId::fromString($command->getAnalysisId());
        $imageId = ImageId::fromString($command->getImageId());
        $this->hotspotDetector->analyze($analysisId->value(), $imageId->value());
    }
}
