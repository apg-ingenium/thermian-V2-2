<?php
declare(strict_types=1);

namespace Hotspot\HotspotDataset\Domain\Dataset;

use DateTime;
use Generator;
use Hotspot\HotspotDataset\Domain\Image\Image;
use Shared\Domain\Uuid;

class InMemoryDataset implements Dataset
{
    private Uuid $id;
    private DatasetName $name;
    private DateTime $dateTime;
    private ?int $size;

    /** @var array<\Hotspot\HotspotDataset\Domain\Image\Image> */
    private array $images;

    public static function empty(Uuid $datasetId, DatasetName $name, ?DateTime $dateTime = null): Dataset
    {
        return new InMemoryDataset($datasetId, $name, [], $dateTime);
    }

    /** @param array<\Hotspot\HotspotDataset\Domain\Image\Image> $images */
    public static function create(Uuid $datasetId, DatasetName $name, array $images, ?DateTime $dateTime = null): Dataset
    {
        return new InMemoryDataset($datasetId, $name, $images, $dateTime);
    }

    /**
     * @param \Shared\Domain\Uuid $id
     * @param array<\Hotspot\HotspotDataset\Domain\Image\Image> $images
     */
    public function __construct(Uuid $id, DatasetName $name, array $images, ?DateTime $dateTime = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->dateTime = $dateTime ?? new DateTime();
        $this->images = $images;
        $this->size = null;
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
        return $this->dateTime;
    }

    /** @return array<\Hotspot\HotspotDataset\Domain\Image\ImageId> */
    public function getImageIds(): array
    {
        return array_map(fn(Image $image) => $image->getId(), $this->images);
    }

    /** @return iterable<\Hotspot\HotspotDataset\Domain\Image\Image> */
    public function getImages(): iterable
    {
        return yield from $this->images;
    }

    public function getNumImages(): int
    {
        return count($this->images);
    }

    /** @return \Generator<iterable<\Hotspot\HotspotDataset\Domain\Image\Image>> */
    public function batchesOfSize(int $size): Generator
    {
        yield from array_chunk($this->images, $size);
    }

    public function getSize(): int
    {
        if (is_null($this->size)) {
            $this->size = array_reduce($this->images, fn($total, Image $image) => $total + $image->getSize(), 0);
        }

        return $this->size;
    }
}
