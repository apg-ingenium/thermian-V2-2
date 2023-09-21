<?php
declare(strict_types=1);

namespace Hotspot\HotspotResults\Domain\HotspotImage;

use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\AnalysisId;
use Hotspot\HotspotDataset\Domain\File\File;
use Hotspot\HotspotDataset\Domain\Image\ImageId;
use Shared\Domain\Uuid;

class InMemoryHotspotImage implements HotspotImage
{
    public static function fromPath(Uuid $id, AnalysisId $analysisId, ImageId $imageId, string $path, ?string $name = null): self
    {
        return new InMemoryHotspotImage($id, $analysisId, $imageId, File::fromPath($path, $name));
    }

    public static function create(Uuid $id, AnalysisId $analysisId, ImageId $imageId, string $content, string $name): self
    {
        return new InMemoryHotspotImage($id, $analysisId, $imageId, File::create($name, $content));
    }

    private Uuid $id;
    private AnalysisId $analysisId;
    private ImageId $imageId;
    private File $file;

    public function __construct(Uuid $id, AnalysisId $analysisId, ImageId $imageId, File $file)
    {
        $this->analysisId = $analysisId;
        $this->imageId = $imageId;
        $this->file = $file;
        $this->id = $id;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getAnalysisId(): AnalysisId
    {
        return $this->analysisId;
    }

    public function getImageId(): ImageId
    {
        return $this->imageId;
    }

    public function getName(): string
    {
        return $this->file->getName();
    }

    public function getFormat(): string
    {
        return $this->file->getFormat();
    }

    public function getSize(): int
    {
        return $this->file->getSize();
    }

    public function getContent(): string
    {
        return $this->file->getContent();
    }

    /** @return resource */
    public function getStream(): mixed
    {
        return $this->file->getStream();
    }

    public function equals(HotspotImage $other): bool
    {
        return $this->getAnalysisId()->equals($other->getAnalysisId())
            && $this->getImageId()->equals($other->getImageId())
            && $this->getName() === $other->getName()
            && $this->getContent() === $other->getContent();
    }
}
