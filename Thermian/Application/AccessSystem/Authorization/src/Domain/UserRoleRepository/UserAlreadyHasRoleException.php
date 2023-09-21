<?php
declare(strict_types=1);

namespace Authorization\Domain\UserRoleRepository;

use Authentication\Domain\User\UserId;
use RuntimeException;
use Throwable;

class UserAlreadyHasRoleException extends RuntimeException
{
    public static function forUser(UserId $userId): self
    {
        $message = "The user with id {$userId->value()} already has a role assigned";

        return new self($message);
    }

    private function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
