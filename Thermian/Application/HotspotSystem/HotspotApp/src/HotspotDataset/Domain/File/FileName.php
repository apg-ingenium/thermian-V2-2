<?php
declare(strict_types=1);

namespace Hotspot\HotspotDataset\Domain\File;

class FileName
{
    private string $value;

    public function __construct(string $name)
    {
        if ($name === '') {
            throw InvalidFileNameException::blank();
        }

        if (strlen($name) > 64) {
            throw InvalidFileNameException::create();
        }

        $this->value = $name;
    }

    public function value(): string
    {
        return $this->value;
    }
}
