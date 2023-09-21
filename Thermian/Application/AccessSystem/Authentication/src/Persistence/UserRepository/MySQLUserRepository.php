<?php
declare(strict_types=1);

namespace Authentication\Persistence\UserRepository;

use Authentication\Domain\User\User;
use Authentication\Domain\User\UserEmail;
use Authentication\Domain\User\UserId;
use Authentication\Domain\User\UserName;
use Authentication\Domain\User\UserPassword;
use Authentication\Domain\UserRepository\DuplicateUserEmailException;
use Authentication\Domain\UserRepository\DuplicateUserIdException;
use Authentication\Domain\UserRepository\UserRepository;
use Cake\Database\Connection;

class MySQLUserRepository implements UserRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function save(User $user): void
    {
        if ($this->containsId($user->getId())) {
            throw DuplicateUserIdException::forId($user->getId());
        }

        if ($this->containsEmail($user->getEmail())) {
            throw DuplicateUserEmailException::forEmail($user->getEmail());
        }

        $sql = 'insert into users (id, name, email, password) values (:id, :name, :email, :password)';

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('id', $user->getId()->binary());
        $statement->bindValue('name', $user->getName()->value());
        $statement->bindValue('email', $user->getEmail()->value());
        $statement->bindValue('password', $user->getPassword()->value());
        $statement->execute();
    }

    private function containsEmail(UserEmail $email): bool
    {
        $sql = 'select id from users where email = :email';

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('email', $email->value());
        $statement->execute();

        $exists = $statement->fetch('assoc');

        return $exists !== false;
    }

    public function containsId(UserId $userId): bool
    {
        $sql = 'select id from users where id = :id';

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('id', $userId->binary());
        $statement->execute();

        $exists = $statement->fetch('assoc');

        return $exists !== false;
    }

    public function findById(UserId $userId): ?User
    {
        $sql = 'select id, name, email, password from users where id = :id';

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('id', $userId->binary());
        $statement->execute();

        $row = $statement->fetch('assoc');

        if (!$row) {
            return null;
        }

        return User::create(
            UserId::fromBinary($row['id']),
            UserName::create($row['name']),
            UserEmail::create($row['email']),
            UserPassword::create($row['password']),
        );
    }

    public function removeById(UserId $userId): void
    {
        $sql = 'delete from users where id = :id';

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('id', $userId->binary());
        $statement->execute();
    }

    public function findByEmail(UserEmail $email): ?User
    {
        $sql = 'select id, name, email, password from users where email = :email';

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('email', $email->value());
        $statement->execute();

        $row = $statement->fetch('assoc');

        if (!$row) {
            return null;
        }

        return User::create(
            UserId::fromBinary($row['id']),
            UserName::create($row['name']),
            UserEmail::create($row['email']),
            UserPassword::create($row['password']),
        );
    }

    public function removeAll(): void
    {
        $this->connection->execute('delete from users');
    }
}
