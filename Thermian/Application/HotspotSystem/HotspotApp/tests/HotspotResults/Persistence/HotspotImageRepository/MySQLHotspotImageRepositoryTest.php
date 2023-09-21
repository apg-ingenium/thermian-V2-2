<?php
declare(strict_types=1);

namespace Hotspot\Test\HotspotResults\Persistence\HotspotImageRepository;

use Cake\Database\Connection;
use Cake\Database\Driver\Mysql;
use Hotspot\HotspotResults\Domain\HotspotImageRepository\HotspotImageRepository;
use Hotspot\HotspotResults\Persistence\HotspotImageRepository\MySQLHotspotImageRepository;
use Hotspot\Test\HotspotResults\Domain\HotspotImageRepository\HotspotImageRepositoryTest;

class MySQLHotspotImageRepositoryTest extends HotspotImageRepositoryTest
{
    protected function getRepository(): HotspotImageRepository
    {
        $connection = new Connection([
            'driver' => new Mysql([
                'host' => env('MYSQL_HOST'),
                'username' => env('MYSQL_USER'),
                'password' => env('MYSQL_PASSWORD'),
                'database' => env('MYSQL_DATABASE'),
            ]),
        ]);

        return new MySQLHotspotImageRepository($connection);
    }
}
