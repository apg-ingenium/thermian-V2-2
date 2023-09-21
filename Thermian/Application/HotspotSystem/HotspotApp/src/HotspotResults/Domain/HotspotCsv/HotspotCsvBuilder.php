<?php
declare(strict_types=1);

namespace Hotspot\HotspotResults\Domain\HotspotCsv;

use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\AnalysisId;
use Hotspot\HotspotDataset\Domain\Image\ImageId;
use RuntimeException;
use Shared\Domain\Uuid;

class HotspotCsvBuilder
{
    public static function hotspotCsv(): self
    {
        return new HotspotCsvBuilder();
    }

    private ?Uuid $id;
    private ?AnalysisId $analysisId;
    private ?ImageId $imageId;
    private ?string $pathToCsv;
    private ?string $content;
    private ?string $name;

    public function __construct()
    {
        $this->id = null;
        $this->analysisId = null;
        $this->imageId = null;
        $this->pathToCsv = null;
        $this->content = null;
        $this->name = null;
    }

    public function fromPath(string $pathToCsv): self
    {
        $this->content = null;
        $this->pathToCsv = $pathToCsv;

        return $this;
    }

    public function withId(Uuid $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function withName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function withContent(string $content): self
    {
        $this->pathToCsv = null;
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

    public function build(): HotspotCsv
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

        if (!is_null($this->pathToCsv)) {
            return InMemoryHotspotCsv::fromPath(
                $this->id,
                $this->analysisId,
                $this->imageId,
                $this->pathToCsv,
                $this->name,
            );
        }

        if (!is_null($this->content)) {
            if (is_null($this->name)) {
                throw new RuntimeException('Csv name is missing');
            }

            return InMemoryHotspotCsv::create(
                $this->id,
                $this->analysisId,
                $this->imageId,
                $this->content,
                $this->name
            );
        }

        throw new RuntimeException('Invalid Builder State');
    }
}
