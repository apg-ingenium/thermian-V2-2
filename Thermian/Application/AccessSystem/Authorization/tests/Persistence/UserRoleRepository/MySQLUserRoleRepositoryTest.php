<?php
declare(strict_types=1);

namespace Authorization\Test\Persistence\UserRoleRepository;

use Authentication\Domain\UserRepository\UserRepository;
use Authentication\Persistence\UserRepository\MySQLUserRepository;
use Authorization\Domain\RoleRepository\RoleRepository;
use Authorization\Domain\UserRoleRepository\UserRoleRepository;
use Authorization\Persistence\RoleRepository\MySQLRoleRepository;
use Authorization\Persistence\UserRoleRepository\MySQLUserRoleRepository;
use Authorization\Test\Domain\UserRoleRepository\UserRoleRepositoryTest;
use Cake\Database\Connection;
use Cake\Database\Driver\Mysql;

class MySQLUserRoleRepositoryTest extends UserRoleRepositoryTest
{
    protected function getRepository(): UserRoleRepository
    {
        $connection = new Connection([
            'driver' => new Mysql([
                'host' => env('MYSQL_HOST'),
                'username' => env('MYSQL_USER'),
                'password' => env('MYSQL_PASSWORD'),
                'database' => env('MYSQL_DATABASE'),
            ]),
        ]);

        return new MySQLUserRoleRepository($connection);
    }

    protected static function getRoleRepository(): RoleRepository
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

    protected function getUserRepository(): UserRepository
    {
        $connection = new Connection([
            'driver' => new Mysql([
                'host' => env('MYSQL_HOST'),
                'username' => env('MYSQL_USER'),
                'password' => env('MYSQL_PASSWORD'),
                'database' => env('MYSQL_DATABASE'),
            ]),
        ]);

        return new MySQLUserRepository($connection);
    }
}
