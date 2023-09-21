<?php
declare(strict_types=1);

namespace Authentication\Domain\UserRepository;

use Authentication\Domain\User\UserEmail;
use RuntimeException;

class DuplicateUserEmailException extends RuntimeException
{
    public static function forEmail(UserEmail $email): self
    {
        return new self($email);
    }

    private function __construct(UserEmail $email)
    {
        parent::__construct("Duplicate user email {$email->value()}");
    }
}
