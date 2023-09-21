<?php
declare(strict_types=1);

namespace Hotspot\HotspotDataset\UseCase\StoreImage;

class StoreImageCommand
{
    private string $id;
    private string $path;
    private string $name;

    public function __construct(string $id, string $name, string $path)
    {
        $this->id = $id;
        $this->name = $name;
        $this->path = $path;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPath(): string
    {
        return $this->path;
    }
}
