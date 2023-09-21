<?php
declare(strict_types=1);

namespace Hotspot\HotspotDataset\Domain\Dataset;

class DatasetName
{
    private string $value;

    public static function create(string $name): self
    {
        return new DatasetName($name);
    }

    private function __construct(string $name)
    {
        $nameLength = strlen($name);

        if ($nameLength === 0 || $nameLength > 64) {
            throw InvalidDatasetNameException::create();
        }

        $this->value = $name;
    }

    public function value(): string
    {
        return $this->value;
    }
}
