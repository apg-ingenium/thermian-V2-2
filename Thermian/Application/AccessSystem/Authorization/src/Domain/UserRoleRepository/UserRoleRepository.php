<?php
declare(strict_types=1);

namespace Authorization\Domain\UserRoleRepository;

use Authentication\Domain\User\UserId;
use Authorization\Domain\Role\Role;

interface UserRoleRepository
{
    public function assignUserRole(UserId $userId, Role $role): void;

    public function updateUserRole(UserId $userId, Role $role): void;

    public function containsUserRole(UserId $userId, Role $role): bool;

    public function findUserRole(UserId $userId): ?Role;

    public function removeUserRole(UserId $userId): void;

    public function removeAllUserRoles(): void;
}
