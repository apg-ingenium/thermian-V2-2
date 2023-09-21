<?php
declare(strict_types=1);

namespace Authorization\Persistence\UserRoleRepository;

use Authentication\Domain\User\UserId;
use Authorization\Domain\Role\Role;
use Authorization\Domain\UserRoleRepository\NonExistentRoleException;
use Authorization\Domain\UserRoleRepository\NonExistentUserException;
use Authorization\Domain\UserRoleRepository\UserAlreadyHasRoleException;
use Authorization\Domain\UserRoleRepository\UserRoleRepository;
use Cake\Database\Connection;
use PDOException;

class MySQLUserRoleRepository implements UserRoleRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function assignUserRole(UserId $userId, Role $role): void
    {
        if (!is_null($this->findUserRole($userId))) {
            throw UserAlreadyHasRoleException::forUser($userId);
        }

        $sql = 'insert into user_roles (user_id, role_id) values (:user_id, (select id from roles where name = :role))';

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('user_id', $userId->binary());
        $statement->bindValue('role', $role->value());

        try {
            $statement->execute();
        }
        catch (PDOException $e) {
            throw match ($statement->errorInfo()[1]) {
                1452 => NonExistentUserException::withId($userId),
                1048 => NonExistentRoleException::forRole($role),
                default => $e,
            };
        }
    }

    public function containsUserRole(UserId $userId, Role $role): bool
    {
        $sql = 'select user_id from user_roles where user_id = :user_id
                and role_id = (select id from roles where name = :role)';

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('user_id', $userId->binary());
        $statement->bindValue('role', $role->value());
        $statement->execute();

        $result = $statement->fetch();

        return $result !== false;
    }

    public function findUserRole(UserId $userId): ?Role
    {
        $sql = 'select name from roles where id = (select role_id from user_roles where user_id = :user_id)';

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('user_id', $userId->binary());
        $statement->execute();

        $row = $statement->fetch('assoc');

        if ($row === false) {
            return null;
        }

        return Role::of($row['name']);
    }

    public function updateUserRole(UserId $userId, Role $role): void
    {
        if (is_null($this->findUserRole($userId))) {
            throw NonExistentUserException::withId($userId);
        }

        $sql = 'update user_roles set role_id = (select id from roles where name = :role) where user_id = :user_id';

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('user_id', $userId->binary());
        $statement->bindValue('role', $role->value());

        try {
            $statement->execute();
        }
        catch (PDOException $e) {
            throw match ($statement->errorInfo()[1]) {
                1048 => NonExistentRoleException::forRole($role),
                default => $e,
            };
        }
    }

    public function removeUserRole(UserId $userId): void
    {
        $sql = 'delete from user_roles where user_id = :user_id';

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('user_id', $userId->binary());
        $statement->execute();
    }

    public function removeAllUserRoles(): void
    {
        $this->connection->execute('delete from user_roles');
    }
}
