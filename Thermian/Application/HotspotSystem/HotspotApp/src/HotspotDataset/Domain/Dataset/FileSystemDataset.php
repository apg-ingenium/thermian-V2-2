<?php
declare(strict_types=1);

namespace Hotspot\HotspotDataset\Domain\Dataset;

use DateTime;
use Generator;
use Hotspot\HotspotDataset\Domain\Image\Image;
use Hotspot\HotspotDataset\Domain\Image\ImageId;
use Shared\Domain\Uuid;

class FileSystemDataset implements Dataset
{
    private Uuid $id;
    private DatasetName $name;
    private DateTime $creationDate;
    private ?int $size;

    /** @var array<string, array<string, mixed>> */
    private array $images;

    /**
     * @param array<string> $imagePaths
     * @param array<string> $imageNames
     * @param array<\Hotspot\HotspotDataset\Domain\Image\ImageId> $imageIds
     */
    public static function create(
        Uuid $datasetId,
        DatasetName $datasetName,
        array $imagePaths,
        array $imageNames,
        ?array $imageIds = null,
        ?DateTime $creationDate = null
    ): self {
        return new FileSystemDataset(
            $datasetId,
            $datasetName,
            $imagePaths,
            $imageNames,
            $imageIds,
            $creationDate
        );
    }

    /**
     * @param array<string> $imagePaths
     * @param array<string> $imageNames
     * @param array<\Hotspot\HotspotDataset\Domain\Image\ImageId> $imageIds
     */
    public function __construct(
        Uuid $id,
        DatasetName $name,
        array $imagePaths,
        array $imageNames,
        ?array $imageIds = null,
        ?DateTime $creationDate = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->creationDate = $creationDate ?? new DateTime();
        $this->size = null;

        if (is_null($imageIds)) {
            $imageIds = array_map(fn() => ImageId::random(), $imagePaths);
        }

        $this->images = [];
        $numImages = count($imagePaths);

        for ($index = 0; $index < $numImages; $index++) {
            $imageId = $imageIds[$index];
            $this->images[$imageId->value()] = [
                'id' => $imageId,
                'path' => $imagePaths[$index],
                'name' => $imageNames[$index],
            ];
        }
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

    /** @return array<\Hotspot\HotspotDataset\Domain\Image\ImageId> */
    public function getImageIds(): array
    {
        return array_map(fn($image) => $image['id'], $this->images);
    }

    public function getNumImages(): int
    {
        return count($this->images);
    }

    public function getSize(): int
    {
        if (is_null($this->size)) {
            $this->size = 0;
            foreach ($this->images as $image) {
                $image = Image::fromPath(
                    $image['id'],
                    $image['path'],
                    $image['name']
                );

                $this->size += $image->getSize();
            }
        }

        return $this->size;
    }

    /** @return iterable<\Hotspot\HotspotDataset\Domain\Image\Image> */
    public function getImages(): iterable
    {
        return $this->convertToImages($this->images);
    }

    /**
     * @param iterable<array<mixed>> $batch
     * @return iterable<\Hotspot\HotspotDataset\Domain\Image\Image>
     */
    private function convertToImages(iterable $batch): iterable
    {
        $images = [];
        foreach ($batch as $image) {
            $images[] = Image::fromPath(
                $image['id'],
                $image['path'],
                $image['name']
            );
        }

        return $images;
    }

    /** @return \Generator<iterable<\Hotspot\HotspotDataset\Domain\Image\Image>> */
    public function batchesOfSize(int $size): Generator
    {
        foreach (array_chunk($this->images, $size) as $batch) {
            yield $this->convertToImages($batch);
        }
    }
}
