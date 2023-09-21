<?php
declare(strict_types=1);

namespace Authorization\Domain\UserRoleRepository;

use Authorization\Domain\Role\Role;
use RuntimeException;
use Throwable;

class NonExistentRoleException extends RuntimeException
{
    public static function forRole(Role $role): self
    {
        $message = "The role {$role->value()} does not exist";

        return new NonExistentRoleException($message);
    }

    private function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
