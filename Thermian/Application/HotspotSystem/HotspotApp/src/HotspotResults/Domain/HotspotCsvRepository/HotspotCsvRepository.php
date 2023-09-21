<?php
declare(strict_types=1);

namespace Hotspot\HotspotResults\Domain\HotspotCsvRepository;

use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\AnalysisId;
use Hotspot\HotspotDataset\Domain\Image\ImageId;
use Hotspot\HotspotResults\Domain\HotspotCsv\HotspotCsv;
use Shared\Domain\Uuid;

interface HotspotCsvRepository
{
    public function save(HotspotCsv $hotspotCsv): void;

    public function containsId(Uuid $id): bool;

    public function findById(Uuid $id): ?HotspotCsv;

    public function removeByAnalysisId(AnalysisId $analysisId): void;

    public function containsRecordId(AnalysisId $analysisId, ImageId $imageId): bool;

    /** @return array<\Hotspot\HotspotResults\Domain\HotspotCsv\HotspotCsv> */
    public function findByRecordId(AnalysisId $analysisId, ImageId $imageId): array;

    public function findByRecordIdAndName(AnalysisId $analysisId, ImageId $imageId, string $name): ?HotspotCsv;

    public function removeByRecordId(AnalysisId $analysisId, ImageId $imageId): void;

    public function removeAll(): void;
}
