<?php
declare(strict_types=1);

namespace Hotspot\HotspotResults\Domain\HotspotImageRepository;

use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\AnalysisId;
use Hotspot\HotspotDataset\Domain\Image\ImageId;
use Hotspot\HotspotResults\Domain\HotspotImage\HotspotImage;

interface HotspotImageRepository
{
    public function save(HotspotImage $image): void;

    public function removeByAnalysisId(AnalysisId $analysisId): void;

    public function containsCompositeId(AnalysisId $analysisId, ImageId $imageId): bool;

    /** @return array<\Hotspot\HotspotResults\Domain\HotspotImage\HotspotImage> */
    public function findByCompositeId(AnalysisId $analysisId, ImageId $imageId): array;

    public function findByAnalysisIdImageIdAndName(AnalysisId $analysisId, ImageId $imageId, string $name): ?HotspotImage;

    public function removeByRecordId(AnalysisId $analysisId, ImageId $imageId): void;

    public function removeAll(): void;
}
