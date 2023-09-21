<?php
declare(strict_types=1);

namespace Hotspot\HotspotDataset\Domain\Dataset;

use DateTime;
use Generator;
use Shared\Domain\Uuid;

interface Dataset
{
    public function getId(): Uuid;

    public function getName(): DatasetName;

    public function getCreationDate(): DateTime;

    /** @return iterable<\Hotspot\HotspotDataset\Domain\Image\Image> */
    public function getImages(): iterable;

    public function getNumImages(): int;

    public function getSize(): int;

    /** @return \Generator<iterable<\Hotspot\HotspotDataset\Domain\Image\Image>> */
    public function batchesOfSize(int $size): Generator;

    /** @return array<\Hotspot\HotspotDataset\Domain\Image\ImageId> */
    public function getImageIds(): array;
}
