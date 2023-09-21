<?php
declare(strict_types=1);

namespace Hotspot\HotspotAnalysis\Domain\HotspotRepository;

use Hotspot\HotspotAnalysis\Domain\Hotspot\HotspotId;
use Hotspot\HotspotAnalysis\Persistence\HotspotRepository\HotspotEntity\HotspotEntity;
use Shared\Domain\Uuid;

interface HotspotRepository
{
    public function save(HotspotEntity $hotspot): void;

    /** @param array<\Hotspot\HotspotAnalysis\Persistence\HotspotRepository\HotspotEntity\HotspotEntity> $hotspots */
    public function saveAll(array $hotspots): void;

    public function containsId(HotspotId $hotspotId): bool;

    /** @param \Hotspot\HotspotAnalysis\Domain\Hotspot\HotspotId[] $hotspotIds */
    public function containsAnyId(array $hotspotIds): bool;

    public function findById(HotspotId $hotspotId): ?HotspotEntity;

    /** @return array<\Hotspot\HotspotAnalysis\Persistence\HotspotRepository\HotspotEntity\HotspotEntity> */
    public function findByPanelId(Uuid $panelId): array;

    /**
     * @param array<\Shared\Domain\Uuid> $panelIds
     * @return array<\Hotspot\HotspotAnalysis\Persistence\HotspotRepository\HotspotEntity\HotspotEntity>
     */
    public function findByMultiplePanelIds(array $panelIds): array;

    public function removeByPanelId(Uuid $PanelId): void;

    /** @param array<\Shared\Domain\Uuid> $panelIds */
    public function removeByMultiplePanelIds(array $panelIds): void;

    public function removeById(HotspotId $hotspotId): void;

    public function removeAll(): void;
}
