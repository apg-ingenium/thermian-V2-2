<?php
declare(strict_types=1);

namespace Authorization\UseCase\AssignUserRole;

class AssignUserRoleCommand
{
    private string $userId;
    private string $role;

    public function __construct(string $userId, string $role)
    {
        $this->userId = $userId;
        $this->role = $role;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getRole(): string
    {
        return $this->role;
    }
}
