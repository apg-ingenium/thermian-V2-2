<?php
declare(strict_types=1);

namespace Hotspot\HotspotDataset\Domain\Image;

use Hotspot\HotspotDataset\Domain\File\File;
use Hotspot\HotspotDataset\Domain\File\FileName;

class Image
{
    public static function fromPath(ImageId $id, string $path, ?string $name = null): self
    {
        if (!is_null($name)) {
            $name = (new FileName($name))->value();
        }

        return new Image($id, File::fromPath($path, $name));
    }

    public static function create(ImageId $id, string $content, string $name): self
    {
        $name = (new FileName($name))->value();

        return new Image($id, File::create($name, $content));
    }

    private ImageId $id;
    private File $file;

    private function __construct(ImageId $id, File $file)
    {
        $this->id = $id;
        $this->file = $file;
    }

    public function getId(): ImageId
    {
        return $this->id;
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

    public function equals(Image $other): bool
    {
        return $this->id->equals($other->id)
            && $this->file->equals($other->file);
    }
}
