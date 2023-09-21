<?php
declare(strict_types=1);

namespace Hotspot\HotspotAnalysis\UseCase\PerformNewImageHotspotAnalysis;

class NewImageHotspotAnalysisCommand
{
    private string $analysisId;
    private string $imageId;
    private string $imagePath;
    private string $imageName;

    public function __construct(string $analysisId, string $imageId, string $imageName, string $imagePath)
    {
        $this->analysisId = $analysisId;
        $this->imageId = $imageId;
        $this->imagePath = $imagePath;
        $this->imageName = $imageName;
    }

    public function getAnalysisId(): string
    {
        return $this->analysisId;
    }

    public function getImageId(): string
    {
        return $this->imageId;
    }

    public function getImagePath(): string
    {
        return $this->imagePath;
    }

    public function getImageName(): string
    {
        return $this->imageName;
    }
}
