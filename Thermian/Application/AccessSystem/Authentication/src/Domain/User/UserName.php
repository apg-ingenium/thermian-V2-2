<?php
declare(strict_types=1);

namespace Authentication\Domain\User;

class UserName
{
    public static function create(string $name): self
    {
        return new UserName($name);
    }

    private string $value;

    private function __construct(string $name)
    {
        if ($name === '') {
            throw InvalidUserNameException::empty();
        }

        $this->value = $name;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(UserName $other): bool
    {
        return $this->value === $other->value;
    }
}
