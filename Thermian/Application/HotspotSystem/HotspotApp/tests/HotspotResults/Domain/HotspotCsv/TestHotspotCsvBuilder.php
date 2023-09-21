<?php
declare(strict_types=1);

namespace Hotspot\Test\HotspotResults\Domain\HotspotCsv;

use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\AnalysisId;
use Hotspot\HotspotDataset\Domain\Image\ImageId;
use Hotspot\HotspotResults\Domain\HotspotCsv\HotspotCsv;
use Hotspot\HotspotResults\Domain\HotspotCsv\HotspotCsvBuilder;
use Shared\Domain\Uuid;

class TestHotspotCsvBuilder
{
    public static function random(): self
    {
        return self::create()
            ->withId(Uuid::random())
            ->withAnalysisId(AnalysisId::random())
            ->withImageId(ImageId::random())
            ->fromPath(self::FILES . 'output-results.csv');
    }

    public static function create(): self
    {
        return new TestHotspotCsvBuilder();
    }

    private const FILES = 'Thermian/Application/HotspotSystem/HotspotApp/tests/Files/';

    private HotspotCsvBuilder $builder;

    private function __construct()
    {
        $this->builder = HotspotCsvBuilder::hotspotCsv();
    }

    public function fromPath(string $pathToCsv): self
    {
        $this->builder->fromPath($pathToCsv);

        return $this;
    }

    public function withId(Uuid $id): self
    {
        $this->builder->withId($id);

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

    public function withContent(string $content): self
    {
        $this->builder->withContent($content);

        return $this;
    }

    public function build(): HotspotCsv
    {
        return $this->builder->build();
    }
}
