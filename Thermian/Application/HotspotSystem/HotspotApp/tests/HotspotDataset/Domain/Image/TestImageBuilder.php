<?php
declare(strict_types=1);

namespace Hotspot\Test\HotspotDataset\Domain\Image;

use Hotspot\HotspotDataset\Domain\Image\Image;
use Hotspot\HotspotDataset\Domain\Image\ImageId;
use RuntimeException;

class TestImageBuilder
{
    private ?ImageId $id;
    private ?string $path;
    private ?string $name;
    private ?string $content;

    public static function random(): self
    {
        return (new TestImageBuilder())
            ->withId(ImageId::random())
            ->fromPath(self::FILES . 'input-image.jpg')
            ->withName('input-image.jpg');
    }

    private const FILES = 'Thermian/Application/HotspotSystem/HotspotApp/tests/HotspotDataset/Files/';

    private function __construct()
    {
        $this->id = null;
        $this->path = null;
        $this->name = null;
    }

    public function withId(ImageId $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function withName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function fromPath(string $pathToImage): self
    {
        $this->path = $pathToImage;

        return $this;
    }

    public function withContent(string $content): self
    {
        $this->content = $content;
        $this->path = null;

        return $this;
    }

    public function build(): Image
    {
        if (is_null($this->id)) {
            throw new RuntimeException('Image id is missing');
        }

        if (!is_null($this->path)) {
            return Image::fromPath($this->id, $this->path, $this->name);
        }

        if (!is_null($this->content)) {
            if (is_null($this->name)) {
                throw new RuntimeException('Image name is missing');
            }

            return Image::create($this->id, $this->content, $this->name);
        }

        throw new RuntimeException('Invalid Builder State');
    }
}
