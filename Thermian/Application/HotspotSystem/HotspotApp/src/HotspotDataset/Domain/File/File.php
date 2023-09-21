<?php
declare(strict_types=1);

namespace Hotspot\HotspotDataset\Domain\File;

use finfo;
use InvalidArgumentException;

class File
{
    public static function fromPath(string $path, ?string $name = ''): self
    {
        $name = $name ?? self::extractFileNameFromPath($path);
        $content = file_get_contents($path);

        if ($content === false) {
            throw new InvalidArgumentException(
                "Invalid file path $path"
            );
        }

        return new File($name, $content);
    }

    private static function extractFileNameFromPath(string $path): string
    {
        $pathSegments = explode('/', $path);

        return end($pathSegments);
    }

    public static function create(string $name, string $content): self
    {
        return new File($name, $content);
    }

    private string $name;
    private string $content;
    private string $format;
    private int $size;

    private function __construct(string $name, string $content)
    {
        $this->name = $name;
        $this->content = $content;
        $this->format = self::guessFormat($content);
        $this->size = strlen($content);
    }

    private static function guessFormat(string $content): string
    {
        $mimeType = (new finfo(FILEINFO_MIME_TYPE))->buffer($content);
        assert($mimeType !== false);

        return explode('/', $mimeType)[1];
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getFormat(): string
    {
        return $this->format;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    /** @return resource */
    public function getStream(): mixed
    {
        $file = fopen('php://memory', 'r+');
        assert($file !== false);
        fwrite($file, $this->content);
        rewind($file);

        return $file;
    }

    public function equals(File $other): bool
    {
        return ($this->getName() === $other->getName())
            && ($this->getContent() === $other->getContent());
    }
}
