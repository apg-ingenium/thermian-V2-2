<?php
declare(strict_types=1);

namespace Hotspot\HotspotDataset\Persistence\ImageRepository;

use Hotspot\HotspotDataset\Domain\Image\Image;
use Hotspot\HotspotDataset\Domain\Image\ImageId;
use Hotspot\HotspotDataset\Domain\ImageRepository\DuplicateImageIdException;
use Hotspot\HotspotDataset\Domain\ImageRepository\ImageRepository;
use Shared\Persistence\DuplicateIdException;

class FileSystemImageRepository implements ImageRepository
{
    private string $directory;

    public function __construct(string $directory = '/app/persistence/image')
    {
        if (!is_dir($directory)) {
            mkdir($directory, 0700, true);
        }

        $this->directory = $directory;
    }

    public function save(Image $image): void
    {
        if ($this->containsId($image->getId())) {
            throw DuplicateImageIdException::forId($image->getId());
        }

        $pathToImage = $this->createPathToImage($image);
        file_put_contents($pathToImage, $image->getContent());
    }

    private function createPathToImage(Image $image): string
    {
        $imageId = $image->getId()->value();
        $name = $image->getName();
        $pathToImage = "{$this->directory}/{$imageId}/{$name}";

        $imageDirectory = dirname($pathToImage);
        if (!is_dir($imageDirectory)) {
            mkdir($imageDirectory, 0700, true);
        }

        return $pathToImage;
    }

    public function containsId(ImageId $imageId): bool
    {
        return (bool)$this->findPathToImage($imageId->value());
    }

    private function findPathToImage(string $imageId): ?string
    {
        $paths = glob("{$this->directory}/{$imageId}/*");

        return $paths ? $paths[0] : null;
    }

    /** @param iterable<\Hotspot\HotspotDataset\Domain\Image\Image> $images */
    public function saveAll(iterable $images): void
    {
        $images = [...$images];
        $imageIds = array_map(fn(Image $image) => $image->getId(), $images);

        if ($this->containsAnyId($imageIds)) {
            throw new DuplicateIdException('Duplicate image id');
        }

        foreach ($images as $image) {
            $this->save($image);
        }
    }

    /** @param array<\Hotspot\HotspotDataset\Domain\Image\ImageId> $imageIds */
    public function containsAnyId(array $imageIds): bool
    {
        $result = false;
        foreach ($imageIds as $imageId) {
            $result = $result || $this->containsId($imageId);
        }

        return $result;
    }

    /** @inheritDoc */
    public function containsAllIds(array $imageIds): bool
    {
        $result = true;
        foreach ($imageIds as $imageId) {
            $result = $result && $this->containsId($imageId);
        }

        return $result;
    }

    public function findById(ImageId $imageId): ?Image
    {
        $pathToImage = $this->findPathToImage($imageId->value());

        return $pathToImage ? Image::fromPath($imageId, $pathToImage, $this->findImageName($pathToImage)) : null;
    }

    /** @inheritDoc */
    public function findAllIdsExcept(array $excludedIds): array
    {
        $indexedIds = [];
        foreach ($excludedIds as $id) {
            $indexedIds[$id->value()] = true;
        }

        return array_filter($this->findAllIds(), fn($id) => !array_key_exists($id->value(), $indexedIds));
    }

    /** @return array<\Hotspot\HotspotDataset\Domain\Image\ImageId> */
    private function findAllIds(): array
    {
        $paths = glob("{$this->directory}/*");
        assert($paths !== false);

        $ids = [];
        foreach ($paths as $path) {
            $segments = explode('/', $path);
            $id = end($segments);
            $ids[] = ImageId::fromString($id);
        }

        return $ids;
    }

    public function findImageName(string $pathToImage): string
    {
        $pathSegments = explode('/', $pathToImage);

        return end($pathSegments);
    }

    public function findImageNames(array $imageIds): array
    {
        $pathsToExistingImages = glob("{$this->directory}/*/*");

        return $pathsToExistingImages
            ? array_map(fn($path) => $this->findImageName($path), $pathsToExistingImages)
            : [];
    }

    public function removeById(ImageId $imageId): void
    {
        $pathToImage = $this->findPathToImage($imageId->value());

        if (is_null($pathToImage)) {
            return;
        }

        unlink($pathToImage);

        $imageDirectory = dirname($pathToImage);
        if ($this->isEmpty($imageDirectory)) {
            rmdir($imageDirectory);
        }
    }

    private function isEmpty(string $directory): bool
    {
        $dir = opendir($directory);
        assert($dir !== false);

        while (($content = readdir($dir)) !== false) {
            if ($content !== '.' && $content !== '..') {
                closedir($dir);

                return false;
            }
        }
        closedir($dir);

        return true;
    }

    /** @inheritDoc */
    public function removeAllById(array $imageIds): void
    {
        foreach ($imageIds as $imageId) {
            $this->removeById($imageId);
        }
    }

    public function removeAll(): void
    {
        $subDirectories = scandir($this->directory);
        assert($subDirectories !== false);
        $imageIds = array_diff($subDirectories, ['.', '..']);

        foreach ($imageIds as $id) {
            $pathToImage = $this->findPathToImage($id);
            if (!is_null($pathToImage)) {
                unlink($pathToImage);
            }

            $imageDirectory = "{$this->directory}/{$id}";
            if ($this->isEmpty($imageDirectory)) {
                rmdir($imageDirectory);
            }
        }
    }
}
