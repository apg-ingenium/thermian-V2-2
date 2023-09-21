<?php
declare(strict_types=1);

namespace Hotspot\HotspotDetection\UseCase\ExecuteImageHotspotDetection;

class ExecuteImageHotspotDetectionCommand
{
    private string $analysisId;
    private string $imageId;

    public function __construct(string $analysisId, string $imageId)
    {
        $this->analysisId = $analysisId;
        $this->imageId = $imageId;
    }

    public function getAnalysisId(): string
    {
        return $this->analysisId;
    }

    public function getImageId(): string
    {
        return $this->imageId;
    }
}
