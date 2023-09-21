<?php
declare(strict_types=1);

namespace Authorization\Domain\RoleRepository;

use Authorization\Domain\Role\Role;
use Shared\Persistence\DuplicateIdException;

class DuplicateRoleException extends DuplicateIdException
{
    public static function forRole(Role $role): self
    {
        return new DuplicateRoleException($role);
    }

    private function __construct(Role $role)
    {
        parent::__construct("Duplicate role {$role->value()}");
    }
}
