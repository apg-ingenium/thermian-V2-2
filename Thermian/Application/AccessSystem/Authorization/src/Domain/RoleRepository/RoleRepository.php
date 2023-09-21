<?php
declare(strict_types=1);

namespace Authorization\Domain\RoleRepository;

use Authorization\Domain\Role\Role;

interface RoleRepository
{
    public function create(Role $role): void;

    public function contains(Role $role): bool;

    /** @return \Authorization\Domain\Role\Role[] */
    public function listAll(): array;

    public function remove(Role $role): void;

    public function removeAll(): void;

    public function reset(): void;
}
