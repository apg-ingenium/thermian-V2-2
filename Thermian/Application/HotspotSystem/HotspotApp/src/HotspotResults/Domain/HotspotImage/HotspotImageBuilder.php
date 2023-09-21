<?php
declare(strict_types=1);

namespace Hotspot\HotspotResults\Domain\HotspotImage;

use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\AnalysisId;
use Hotspot\HotspotDataset\Domain\Image\ImageId;
use RuntimeException;
use Shared\Domain\Uuid;

class HotspotImageBuilder
{
    public static function hotspotImage(): self
    {
        return new HotspotImageBuilder();
    }

    private ?Uuid $id;
    private ?AnalysisId $analysisId;
    private ?ImageId $imageId;
    private ?string $name;
    private ?string $content;
    private ?string $pathToImage;

    public function __construct()
    {
        $this->id = null;
        $this->analysisId = null;
        $this->imageId = null;
        $this->name = null;
        $this->pathToImage = null;
        $this->content = null;
    }

    public function withName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function fromPath(string $pathToImage): self
    {
        $this->content = null;
        $this->pathToImage = $pathToImage;

        return $this;
    }

    public function withContent(string $content): self
    {
        $this->pathToImage = null;
        $this->content = $content;

        return $this;
    }

    public function withAnalysisId(AnalysisId $analysisId): self
    {
        $this->analysisId = $analysisId;

        return $this;
    }

    public function withImageId(ImageId $imageId): self
    {
        $this->imageId = $imageId;

        return $this;
    }

    public function build(): HotspotImage
    {
        if (is_null($this->id)) {
            $this->id = Uuid::random();
        }

        if (is_null($this->analysisId)) {
            throw new RuntimeException('Analysis id is missing');
        }

        if (is_null($this->imageId)) {
            throw new RuntimeException('Image id is missing');
        }

        if (!is_null($this->pathToImage)) {
            return InMemoryHotspotImage::fromPath(
                $this->id,
                $this->analysisId,
                $this->imageId,
                $this->pathToImage,
                $this->name,
            );
        }

        if (!is_null($this->content)) {
            if (is_null($this->name)) {
                throw new RuntimeException('Image name is missing');
            }

            return InMemoryHotspotImage::create(
                $this->id,
                $this->analysisId,
                $this->imageId,
                $this->content,
                $this->name,
            );
        }

        throw new RuntimeException('Invalid Builder State');
    }
}
