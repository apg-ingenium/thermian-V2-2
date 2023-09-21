<?php
declare(strict_types=1);

namespace Hotspot\HotspotDataset\Domain\ImageRepository;

use Hotspot\HotspotDataset\Domain\Image\Image;
use Hotspot\HotspotDataset\Domain\Image\ImageId;

interface ImageRepository
{
    public function save(Image $image): void;

    /** @param iterable<\Hotspot\HotspotDataset\Domain\Image\Image> $images */
    public function saveAll(iterable $images): void;

    public function containsId(ImageId $imageId): bool;

    /** @param array<\Hotspot\HotspotDataset\Domain\Image\ImageId> $imageIds */
    public function containsAnyId(array $imageIds): bool;

    /** @param array<\Hotspot\HotspotDataset\Domain\Image\ImageId> $imageIds */
    public function containsAllIds(array $imageIds): bool;

    public function findById(ImageId $imageId): ?Image;

    /**
     * @param array<\Hotspot\HotspotDataset\Domain\Image\ImageId> $excludedIds
     * @return array<\Hotspot\HotspotDataset\Domain\Image\ImageId>
     */
    public function findAllIdsExcept(array $excludedIds): array;

    public function removeById(ImageId $imageId): void;

    public function removeAll(): void;

    /**
     * @param array<\Hotspot\HotspotDataset\Domain\Image\ImageId> $imageIds
     * @return array<string>
     */
    public function findImageNames(array $imageIds): array;

    /** @param array<\Hotspot\HotspotDataset\Domain\Image\ImageId> $imageIds */
    public function removeAllById(array $imageIds): void;
}
