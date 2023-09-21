<?php
declare(strict_types=1);

namespace Authorization\UseCase\AssignUserRole;

use Authentication\Domain\User\UserId;
use Authorization\Domain\Role\Role;
use Authorization\Domain\UserRoleRepository\UserRoleRepository;

class AssignUserRoleUseCase
{
    private UserRoleRepository $userRoleRepository;

    public function __construct(
        UserRoleRepository $userRoleRepository,
    ) {
        $this->userRoleRepository = $userRoleRepository;
    }

    public function execute(AssignUserRoleCommand $command): void
    {
        $userId = UserId::fromString($command->getUserId());
        $role = Role::of($command->getRole());
        $this->userRoleRepository->assignUserRole($userId, $role);
    }
}
