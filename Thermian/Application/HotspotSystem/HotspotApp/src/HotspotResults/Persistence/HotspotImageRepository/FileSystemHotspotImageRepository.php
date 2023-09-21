<?php
declare(strict_types=1);

namespace Hotspot\HotspotResults\Persistence\HotspotImageRepository;

use Generator;
use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\AnalysisId;
use Hotspot\HotspotDataset\Domain\Image\ImageId;
use Hotspot\HotspotResults\Domain\HotspotImage\HotspotImage;
use Hotspot\HotspotResults\Domain\HotspotImage\HotspotImageBuilder;
use Hotspot\HotspotResults\Domain\HotspotImageRepository\HotspotImageRepository;

class FileSystemHotspotImageRepository implements HotspotImageRepository
{
    private string $directory;

    public function __construct(string $directory = '/app/persistence/analysis')
    {
        if (!is_dir($directory)) {
            mkdir($directory, 0700, true);
        }

        $this->directory = $directory;
    }

    public function save(HotspotImage $image): void
    {
        $pathToImage = $this->createPathToImage($image);
        file_put_contents($pathToImage, $image->getContent());
    }

    public function findByAnalysisIdImageIdAndName(AnalysisId $analysisId, ImageId $imageId, string $name): ?HotspotImage
    {
        $path = $this->findPathByAnalysisIdImageIdAndName($analysisId->value(), $imageId->value(), $name);

        return $path ? HotspotImageBuilder::hotspotImage()
                ->withAnalysisId($analysisId)
                ->withImageId($imageId)
                ->fromPath($path)
                ->build()
            : null;
    }

    public function findPathByAnalysisIdImageIdAndName(string $analysisId, string $imageId, string $name): ?string
    {
        $path = glob("{$this->directory}/{$analysisId}/{$imageId}/{$name}*");

        return $path ? $path[0] : null;
    }

    private function createPathToImage(HotspotImage $image): string
    {
        $analysisId = $image->getAnalysisId()->value();
        $imageId = $image->getImageId()->value();
        $imageName = $image->getName();

        $pathToImage = "{$this->directory}/{$analysisId}/{$imageId}/{$imageName}";

        $imageDirectory = dirname($pathToImage);
        if (!is_dir($imageDirectory)) {
            mkdir($imageDirectory, 0700, true);
        }

        return $pathToImage;
    }

    public function containsCompositeId(AnalysisId $analysisId, ImageId $imageId): bool
    {
        return (bool)$this->findPathsToImages($analysisId->value(), $imageId->value());
    }

    /** @return array<string> */
    private function findPathsToImages(string $analysisId, string $imageId): array
    {
        $paths = glob("{$this->directory}/{$analysisId}/{$imageId}/*");

        return $paths ?: [];
    }

    /** @return array<\Hotspot\HotspotResults\Domain\HotspotImage\HotspotImage> */
    public function findByCompositeId(AnalysisId $analysisId, ImageId $imageId): array
    {
        $paths = $this->findPathsToImages($analysisId->value(), $imageId->value());

        $images = [];

        foreach ($paths as $path) {
            $images[] = HotspotImageBuilder::hotspotImage()
                ->withAnalysisId($analysisId)
                ->withImageId($imageId)
                ->fromPath($path)
                ->build();
        }

        return $images;
    }

    public function removeByRecordId(AnalysisId $analysisId, ImageId $imageId): void
    {
        $paths = $this->findPathsToImages($analysisId->value(), $imageId->value());

        if (!$paths) {
            return;
        }

        foreach ($paths as $path) {
            unlink($path);
        }

        $imageDirectory = dirname($paths[0]);
        if ($this->isEmpty($imageDirectory)) {
            rmdir($imageDirectory);
        }

        $analysisDirectory = dirname($imageDirectory);
        if ($this->isEmpty($analysisDirectory)) {
            rmdir($analysisDirectory);
        }
    }

    public function removeByAnalysisId(AnalysisId $analysisId): void
    {
        system("rm -rf {$this->directory}/{$analysisId->value()}");
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

    public function removeAll(): void
    {
        foreach ($this->iterateOverAnalysisIds() as $analysisId) {
            $analysisDirectory = "{$this->directory}/{$analysisId}";
            foreach ($this->iterateOverImageIds($analysisId) as $imageId) {
                $paths = $this->findPathsToImages($analysisId, $imageId);
                foreach ($paths as $path) {
                    unlink($path);
                }
                $imageDirectory = "{$analysisDirectory}/{$imageId}";
                if ($this->isEmpty($imageDirectory)) {
                    rmdir($imageDirectory);
                }
            }
            if ($this->isEmpty($analysisDirectory)) {
                rmdir($analysisDirectory);
            }
        }
    }

    /** @return \Generator<string> */
    private function iterateOverContent(string $directory): Generator
    {
        $contents = scandir($directory);
        assert($contents !== false);
        yield from array_diff($contents, ['.', '..']);
    }

    /** @return \Generator<string> */
    private function iterateOverAnalysisIds(): Generator
    {
        yield from $this->iterateOverContent($this->directory);
    }

    /** @return \Generator<string> */
    private function iterateOverImageIds(string $analysisId): Generator
    {
        $analysisDirectory = "{$this->directory}/{$analysisId}";
        yield from $this->iterateOverContent($analysisDirectory);
    }
}
