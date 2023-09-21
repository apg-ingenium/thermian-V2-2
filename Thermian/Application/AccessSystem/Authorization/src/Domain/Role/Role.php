<?php
declare(strict_types=1);

namespace Authorization\Domain\Role;

class Role
{
    private const ROLES = ['admin', 'analyst', 'editor'];

    public static function of(string $role): self
    {
        return new Role($role);
    }

    /** @return string[] */
    public static function values(): array
    {
        return self::ROLES;
    }

    private string $value;

    private function __construct(string $role)
    {
        if (!in_array(strtolower($role), self::ROLES)) {
            throw InvalidRoleException::forRole($role);
        }

        $this->value = $role;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(Role $other): bool
    {
        return $this->value() === $other->value();
    }
}
