<?php
declare(strict_types=1);

namespace Hotspot\HotspotResults\Domain\HotspotCsv;

use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\AnalysisId;
use Hotspot\HotspotDataset\Domain\File\File;
use Hotspot\HotspotDataset\Domain\Image\ImageId;
use Shared\Domain\Uuid;

class InMemoryHotspotCsv implements HotspotCsv
{
    public static function fromPath(Uuid $id, AnalysisId $analysisId, ImageId $imageId, string $pathToCsv, ?string $name = null): self
    {
        return new InMemoryHotspotCsv($id, $analysisId, $imageId, File::fromPath($pathToCsv, $name));
    }

    public static function create(Uuid $id, AnalysisId $analysisId, ImageId $imageId, string $content, string $name): self
    {
        return new InMemoryHotspotCsv($id, $analysisId, $imageId, File::create($name, $content));
    }

    private AnalysisId $analysisId;
    private ImageId $imageId;
    private File $file;
    private Uuid $id;

    private function __construct(Uuid $id, AnalysisId $analysisId, ImageId $imageId, File $file)
    {
        $this->id = $id;
        $this->analysisId = $analysisId;
        $this->imageId = $imageId;
        $this->file = $file;
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

    public function equals(HotspotCsv $other): bool
    {
        return $this->getId()->equals($other->getId())
            && $this->getAnalysisId()->equals($other->getAnalysisId())
            && $this->getImageId()->equals($other->getImageId())
            && $this->getName() === $other->getName()
            && $this->getContent() === $other->getContent();
    }
}
