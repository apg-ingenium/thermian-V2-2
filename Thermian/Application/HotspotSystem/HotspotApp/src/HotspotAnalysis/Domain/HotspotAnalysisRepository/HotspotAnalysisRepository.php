<?php
declare(strict_types=1);

namespace Hotspot\HotspotAnalysis\Domain\HotspotAnalysisRepository;

use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\AnalysisId;
use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\HotspotAnalysis;
use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\HotspotAnalysisRecord;
use Hotspot\HotspotDataset\Domain\Image\ImageId;

interface HotspotAnalysisRepository
{
    public function saveAnalysis(HotspotAnalysis $analysis): void;

    public function findAnalysisById(AnalysisId $analysisId): ?HotspotAnalysis;

    public function removeAnalysisById(AnalysisId $analysisId): void;

    public function saveAnalysisRecord(HotspotAnalysisRecord $analysisRecord): void;

    public function containsAnalysisRecordId(AnalysisId $analysisId, ImageId $imageId): bool;

    public function findAnalysisRecordById(AnalysisId $analysisId, ImageId $imageId): ?HotspotAnalysisRecord;

    public function removeAnalysisRecordById(AnalysisId $analysisId, ImageId $imageId): void;

    public function removeAll(): void;
}
