<?php
declare(strict_types=1);

namespace Hotspot\Test\HotspotResults\Domain\HotspotImage;

use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\AnalysisId;
use Hotspot\HotspotDataset\Domain\Image\ImageId;
use Hotspot\HotspotResults\Domain\HotspotImage\HotspotImage;
use Hotspot\HotspotResults\Domain\HotspotImage\HotspotImageBuilder;

class TestHotspotImageBuilder
{
    public static function random(): self
    {
        return self::create()
            ->withAnalysisId(AnalysisId::random())
            ->withImageId(ImageId::random())
            ->fromPath(self::FILES . 'output-image.png');
    }

    public static function create(): self
    {
        return new TestHotspotImageBuilder();
    }

    private const FILES = 'Thermian/Application/HotspotSystem/HotspotApp/tests/Files/';

    private HotspotImageBuilder $builder;

    private function __construct()
    {
        $this->builder = HotspotImageBuilder::hotspotImage();
    }

    public function fromPath(string $pathToImage): self
    {
        $this->builder->fromPath($pathToImage);

        return $this;
    }

    public function withContent(string $content): self
    {
        $this->builder->withContent($content);

        return $this;
    }

    public function withAnalysisId(AnalysisId $analysisId): self
    {
        $this->builder->withAnalysisId($analysisId);

        return $this;
    }

    public function withImageId(ImageId $imageId): self
    {
        $this->builder->withImageId($imageId);

        return $this;
    }

    public function withName(string $name): self
    {
        $this->builder->withName($name);

        return $this;
    }

    public function build(): HotspotImage
    {
        return $this->builder->build();
    }
}
