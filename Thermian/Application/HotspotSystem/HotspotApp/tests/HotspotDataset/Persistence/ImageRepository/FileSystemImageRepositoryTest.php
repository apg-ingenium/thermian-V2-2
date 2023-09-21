<?php
declare(strict_types=1);

namespace Hotspot\Test\HotspotDataset\Persistence\ImageRepository;

use Hotspot\HotspotDataset\Domain\ImageRepository\ImageRepository;
use Hotspot\HotspotDataset\Persistence\ImageRepository\FileSystemImageRepository;
use Hotspot\Test\HotspotDataset\Domain\ImageRepository\ImageRepositoryTest;

class FileSystemImageRepositoryTest extends ImageRepositoryTest
{
    protected function getRepository(): ImageRepository
    {
        return new FileSystemImageRepository();
    }
}
