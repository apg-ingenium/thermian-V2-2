<?php
declare(strict_types=1);

namespace Authorization\Test\UseCase\AssignUserRole;

use Authentication\Domain\User\UserId;
use Authorization\Domain\Role\Role;
use Authorization\UseCase\AssignUserRole\AssignUserRoleCommand;

class AssignUserRoleCommandBuilder
{
    public static function assignUserRoleCommand(): self
    {
        return new self();
    }

    private string $userId;
    private string $role;

    public function __construct()
    {
        $this->userId = UserId::random()->value();
        $this->role = Role::of('analyst')->value();
    }

    public function withUserId(string $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function toUserWithInvalidId(): self
    {
        $this->userId = '';

        return $this;
    }

    public function withRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function withInvalidRole(): self
    {
        $this->role = '';

        return $this;
    }

    public function build(): AssignUserRoleCommand
    {
        return new AssignUserRoleCommand(
            $this->userId,
            $this->role
        );
    }
}
