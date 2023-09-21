<?php
declare(strict_types=1);

namespace Authentication\Test\Persistence\UserRepository;

use Authentication\Domain\UserRepository\UserRepository;
use Authentication\Persistence\UserRepository\MySQLUserRepository;
use Authentication\Test\Domain\UserRepository\UserRepositoryTest;
use Cake\Database\Connection;
use Cake\Database\Driver\Mysql;

class MySQLUserRepositoryTest extends UserRepositoryTest
{
    protected function getRepository(): UserRepository
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
