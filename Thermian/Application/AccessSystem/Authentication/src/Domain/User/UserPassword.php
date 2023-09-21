<?php
declare(strict_types=1);

namespace Authentication\Domain\User;

class UserPassword
{
    public static function create(string $password): self
    {
        return new UserPassword($password);
    }

    private string $value;

    private function __construct(string $password)
    {
        if ($password === '') {
            throw InvalidUserPasswordException::empty();
        }

        $this->value = $password;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(UserPassword $other): bool
    {
        return $this->value === $other->value;
    }
}
