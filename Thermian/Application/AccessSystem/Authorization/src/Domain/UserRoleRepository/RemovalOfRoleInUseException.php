<?php
declare(strict_types=1);

namespace Authorization\Domain\UserRoleRepository;

use Authorization\Domain\Role\Role;
use RuntimeException;
use Throwable;

class RemovalOfRoleInUseException extends RuntimeException
{
    public static function forRole(Role $role): self
    {
        $message = "Attempted to remove the role of {$role->value()}, currently in use";

        return new RemovalOfRoleInUseException($message);
    }

    public static function forSomeRole(): self
    {
        $message = 'Attempted to remove some role/s in use';

        return new RemovalOfRoleInUseException($message);
    }

    private function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
