<?php
declare(strict_types=1);

namespace Authorization\Test\Persistence\RoleRepository;

use Authorization\Domain\RoleRepository\RoleRepository;
use Authorization\Persistence\RoleRepository\MySQLRoleRepository;
use Authorization\Test\Domain\RoleRepository\RoleRepositoryTest;
use Cake\Database\Connection;
use Cake\Database\Driver\Mysql;

class MySQLRoleRepositoryTest extends RoleRepositoryTest
{
    protected static function getRepository(): RoleRepository
    {
        $connection = new Connection([
            'driver' => new Mysql([
                'host' => env('MYSQL_HOST'),
                'username' => env('MYSQL_USER'),
                'password' => env('MYSQL_PASSWORD'),
                'database' => env('MYSQL_DATABASE'),
            ]),
        ]);

        return new MySQLRoleRepository($connection);
    }
}
