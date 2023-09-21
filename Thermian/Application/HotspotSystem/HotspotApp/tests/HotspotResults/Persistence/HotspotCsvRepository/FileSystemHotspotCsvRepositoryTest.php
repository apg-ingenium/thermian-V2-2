<?php
declare(strict_types=1);

namespace Hotspot\Test\HotspotResults\Persistence\HotspotCsvRepository;

use Hotspot\HotspotResults\Domain\HotspotCsvRepository\HotspotCsvRepository;
use Hotspot\HotspotResults\Persistence\HotspotCsvRepository\FileSystemHotspotCsvRepository;
use Hotspot\Test\HotspotResults\Domain\HotspotCsvRepository\HotspotCsvRepositoryTest;

class FileSystemHotspotCsvRepositoryTest extends HotspotCsvRepositoryTest
{
    protected function getRepository(): HotspotCsvRepository
    {
        return new FileSystemHotspotCsvRepository();
    }
}
