<?php
declare(strict_types=1);

namespace Hotspot\HotspotDataset\Domain\Dataset;

use DateTime;
use Shared\Domain\Uuid;

class DatasetStats
{
    private Uuid $id;
    private DatasetName $name;
    private DateTime $creationDate;
    private int $size;

    /** @var array<\Hotspot\HotspotDataset\Domain\Image\ImageId> */
    private array $imageIds;

    /** @param array<\Hotspot\HotspotDataset\Domain\Image\ImageId> $imageIds */
    public static function create(Uuid $id, DatasetName $name, int $size, DateTime $creationDate, array $imageIds): self
    {
        return new DatasetStats($id, $name, $size, $creationDate, $imageIds);
    }

    /** @param array<\Hotspot\HotspotDataset\Domain\Image\ImageId> $imageIds */
    private function __construct(Uuid $id, DatasetName $name, int $size, DateTime $creationDate, array $imageIds)
    {
        $this->id = $id;
        $this->name = $name;
        $this->size = $size;
        $this->creationDate = $creationDate;
        $this->imageIds = $imageIds;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getName(): DatasetName
    {
        return $this->name;
    }

    public function getCreationDate(): DateTime
    {
        return $this->creationDate;
    }

    public function getNumImages(): int
    {
        return count($this->imageIds);
    }

    public function getSize(): int
    {
        return $this->size;
    }

    /** @return array<\Hotspot\HotspotDataset\Domain\Image\ImageId> */
    public function getImageIds(): array
    {
        return $this->imageIds;
    }
}
