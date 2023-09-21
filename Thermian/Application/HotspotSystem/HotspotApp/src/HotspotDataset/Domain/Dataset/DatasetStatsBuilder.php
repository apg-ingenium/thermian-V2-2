<?php
declare(strict_types=1);

namespace Hotspot\HotspotDataset\Domain\Dataset;

use DateTime;
use Hotspot\HotspotDataset\Domain\Image\ImageId;
use InvalidArgumentException;
use Shared\Domain\Uuid;

class DatasetStatsBuilder
{
    private ?Uuid $id;
    private ?DatasetName $name;
    private ?DateTime $creationDate;
    private ?int $size;

    /** @var array<\Hotspot\HotspotDataset\Domain\Image\ImageId> */
    private array $imageIds;

    public static function datasetStats(): self
    {
        return new DatasetStatsBuilder();
    }

    private function __construct()
    {
        $this->id = null;
        $this->name = null;
        $this->size = null;
        $this->creationDate = null;
        $this->imageIds = [];
    }

    public function withId(Uuid $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function withName(DatasetName $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function createdAt(DateTime $creationDate): self
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function withImageId(ImageId $imageId): self
    {
        $this->imageIds[] = $imageId;

        return $this;
    }

    /** @param \Hotspot\HotspotDataset\Domain\Image\ImageId[] $imageIds */
    public function withImageIds(array $imageIds): self
    {
        $this->imageIds = $imageIds;

        return $this;
    }

    public function withSize(int $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function build(): DatasetStats
    {
        if (is_null($this->id)) {
            throw new InvalidArgumentException('dataset id is missing');
        }

        if (is_null($this->name)) {
            throw new InvalidArgumentException('dataset name is missing');
        }

        if (is_null($this->creationDate)) {
            throw new InvalidArgumentException('dataset creation date is missing');
        }

        if (is_null($this->size)) {
            throw new InvalidArgumentException('dataset size is missing');
        }

        return DatasetStats::create(
            $this->id,
            $this->name,
            $this->size,
            $this->creationDate,
            $this->imageIds
        );
    }
}
