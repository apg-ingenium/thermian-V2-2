<?php
declare(strict_types=1);

namespace Hotspot\HotspotResults\UseCase\FindHotspotImage;

use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\AnalysisId;
use Hotspot\HotspotDataset\Domain\Image\ImageId;
use Hotspot\HotspotResults\Domain\HotspotImage\HotspotImage;
use Hotspot\HotspotResults\Domain\HotspotImageRepository\HotspotImageRepository;

class FindHotspotImageUseCase
{
    private HotspotImageRepository $repository;

    public function __construct(HotspotImageRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(FindHotspotImageQuery $query): ?HotspotImage
    {
        $analysisId = AnalysisId::fromString($query->getAnalysisId());
        $imageId = ImageId::fromString($query->getImageId());
        $imageName = 'bounding-boxes';

        return $this->repository->findByAnalysisIdImageIdAndName($analysisId, $imageId, $imageName);
    }
}
