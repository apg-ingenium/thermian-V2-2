<?php
declare(strict_types=1);

namespace Hotspot\Test\HotspotResults\Persistence\HotspotImageRepository;

use Hotspot\HotspotResults\Domain\HotspotImageRepository\HotspotImageRepository;
use Hotspot\HotspotResults\Persistence\HotspotImageRepository\FileSystemHotspotImageRepository;
use Hotspot\Test\HotspotResults\Domain\HotspotImageRepository\HotspotImageRepositoryTest;

class FileSystemHotspotImageRepositoryTest extends HotspotImageRepositoryTest
{
    protected function getRepository(): HotspotImageRepository
    {
        return new FileSystemHotspotImageRepository();
    }
}
