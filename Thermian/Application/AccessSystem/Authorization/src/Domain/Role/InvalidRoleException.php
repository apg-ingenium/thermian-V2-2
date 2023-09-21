<?php
declare(strict_types=1);

namespace Authorization\Domain\Role;

use RuntimeException;
use Throwable;

class InvalidRoleException extends RuntimeException
{
    public static function forRole(string $role): self
    {
        $message = "invalid user role $role";

        return new self($message);
    }

    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
