<?php
declare(strict_types=1);

namespace Authorization\Persistence\RoleRepository;

use Authorization\Domain\Role\Role;
use Authorization\Domain\RoleRepository\DuplicateRoleException;
use Authorization\Domain\RoleRepository\RoleRepository;
use Authorization\Domain\UserRoleRepository\RemovalOfRoleInUseException;
use Cake\Database\Connection;

class MySQLRoleRepository implements RoleRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function create(Role $role): void
    {
        if ($this->contains($role)) {
            throw DuplicateRoleException::forRole($role);
        }

        $sql = 'insert into roles (name) values (:role)';

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('role', $role->value());
        $statement->execute();
    }

    public function contains(Role $role): bool
    {
        $sql = 'select id from roles where name = :role';

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('role', $role->value());
        $statement->execute();

        $exists = $statement->fetch('assoc');

        return $exists !== false;
    }

    /** @inheritDoc */
    public function listAll(): array
    {
        $statement = $this->connection->execute('select name from roles');

        $roles = [];
        while (($row = $statement->fetch('assoc'))) {
            $roles[] = Role::of($row['name']);
        }

        return $roles;
    }

    public function remove(Role $role): void
    {
        if ($this->isRoleInUse($role)) {
            throw RemovalOfRoleInUseException::forRole($role);
        }

        $sql = 'delete from roles where name = :role';

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('role', $role->value());
        $statement->execute();
    }

    private function isRoleInUse(Role $role): bool
    {
        $sql = 'select role_id from user_roles where role_id = (select id from roles where name = :role) limit 1';

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('role', $role->value());
        $statement->execute();

        $result = $statement->fetch();

        return $result !== false;
    }

    public function removeAll(): void
    {
        if ($this->hasAnyRoleInUse()) {
            throw RemovalOfRoleInUseException::forSomeRole();
        }

        $this->connection->execute('delete from roles');
    }

    private function hasAnyRoleInUse(): bool
    {
        $statement = $this->connection->execute('select role_id from user_roles limit 1');
        $result = $statement->fetch();

        return $result !== false;
    }

    public function reset(): void
    {
        $this->removeAll();

        foreach (Role::values() as $role) {
            $this->create(Role::of($role));
        }
    }
}
