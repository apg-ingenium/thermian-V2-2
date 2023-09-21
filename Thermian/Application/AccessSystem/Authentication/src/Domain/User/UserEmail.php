<?php
declare(strict_types=1);

namespace Authentication\Domain\User;

class UserEmail
{
    public static function create(string $email): self
    {
        return new UserEmail($email);
    }

    private string $value;

    private function __construct(string $email)
    {
        if ($email === '') {
            throw InvalidUserEmailException::empty();
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw InvalidUserEmailException::invalid();
        }

        $this->value = strtolower($email);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(UserEmail $other): bool
    {
        return $this->value === $other->value;
    }
}
