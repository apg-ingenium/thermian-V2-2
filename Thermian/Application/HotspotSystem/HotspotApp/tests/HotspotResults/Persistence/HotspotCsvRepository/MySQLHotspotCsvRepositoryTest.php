<?php
declare(strict_types=1);

namespace Hotspot\Test\HotspotResults\Persistence\HotspotCsvRepository;

use Cake\Database\Connection;
use Cake\Database\Driver\Mysql;
use Hotspot\HotspotResults\Domain\HotspotCsvRepository\HotspotCsvRepository;
use Hotspot\HotspotResults\Persistence\HotspotCsvRepository\MySQLHotspotCsvRepository;
use Hotspot\Test\HotspotResults\Domain\HotspotCsvRepository\HotspotCsvRepositoryTest;

class MySQLHotspotCsvRepositoryTest extends HotspotCsvRepositoryTest
{
    protected function getRepository(): HotspotCsvRepository
    {
        $connection = new Connection([
            'driver' => new Mysql([
                'host' => env('MYSQL_HOST'),
                'username' => env('MYSQL_USER'),
                'password' => env('MYSQL_PASSWORD'),
                'database' => env('MYSQL_DATABASE'),
            ]),
        ]);

        return new MySQLHotspotCsvRepository($connection);
    }
}
