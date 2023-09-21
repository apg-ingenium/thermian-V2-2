<?php
declare(strict_types=1);

namespace Authorization\Domain\UserRoleRepository;

use Authentication\Domain\User\UserId;
use RuntimeException;
use Throwable;

class NonExistentUserException extends RuntimeException
{
    public static function withId(UserId $userId): self
    {
        $message = "No user exists with id {$userId->value()}";

        return new NonExistentUserException($message);
    }

    private function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
