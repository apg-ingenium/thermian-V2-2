<?php
declare(strict_types=1);

namespace Hotspot\HotspotAnalysis\Domain\PanelRepository;

use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\AnalysisId;
use Hotspot\HotspotAnalysis\Persistence\PanelRepository\PanelEntity\PanelEntity;
use Hotspot\HotspotDataset\Domain\Image\ImageId;

interface PanelRepository
{
    public function save(PanelEntity $panel): void;

    /** @param array<\Hotspot\HotspotAnalysis\Persistence\PanelRepository\PanelEntity\PanelEntity> $panels */
    public function saveAll(array $panels): void;

    /** @return array<\Hotspot\HotspotAnalysis\Persistence\PanelRepository\PanelEntity\PanelEntity> */
    public function findByAnalysisId(AnalysisId $analysisId): array;

    /** @return array<\Hotspot\HotspotAnalysis\Persistence\PanelRepository\PanelEntity\PanelEntity> */
    public function findByRecordId(AnalysisId $analysisId, ImageId $imageId): array;

    /** @return array<\Shared\Domain\Uuid> */
    public function findPanelIdsByRecordId(AnalysisId $analysisId, ImageId $imageId): array;

    public function removeByRecordId(AnalysisId $analysisId, ImageId $imageId): void;

    public function removeAll(): void;
}
