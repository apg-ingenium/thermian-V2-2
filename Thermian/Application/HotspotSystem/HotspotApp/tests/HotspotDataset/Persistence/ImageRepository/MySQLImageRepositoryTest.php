<?php
declare(strict_types=1);

namespace Hotspot\Test\HotspotDataset\Persistence\ImageRepository;

use Cake\Database\Connection;
use Cake\Database\Driver\Mysql;
use Hotspot\HotspotDataset\Domain\ImageRepository\ImageRepository;
use Hotspot\HotspotDataset\Persistence\ImageRepository\MySQLImageRepository;
use Hotspot\Test\HotspotDataset\Domain\ImageRepository\ImageRepositoryTest;

class MySQLImageRepositoryTest extends ImageRepositoryTest
{
    protected function getRepository(): ImageRepository
    {
        $connection = new Connection([
            'driver' => new Mysql([
                'host' => env('MYSQL_HOST'),
                'username' => env('MYSQL_USER'),
                'password' => env('MYSQL_PASSWORD'),
                'database' => env('MYSQL_DATABASE'),
            ]),
        ]);

        return new MySQLImageRepository($connection);
    }
}
