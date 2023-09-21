<?php
declare(strict_types=1);

namespace Authentication\UseCase\CreateUser;

use Authentication\Domain\User\User;
use Authentication\Domain\UserRepository\UserRepository;
use Authorization\Domain\Role\Role;
use Authorization\Domain\UserRoleRepository\UserRoleRepository;
use Cake\Database\Connection;

class CreateUserTransaction
{
    private UserRepository $userRepository;
    private UserRoleRepository $userRoleRepository;
    private Connection $connection;

    public function __construct(UserRepository $userRepository, UserRoleRepository $userRoleRepository, Connection $connection)
    {
        $this->userRepository = $userRepository;
        $this->userRoleRepository = $userRoleRepository;
        $this->connection = $connection;
    }

    public function execute(User $user, Role $role): void
    {
        $this->connection->transactional(function (Connection $connection) use ($user, $role) {
            $userId = $user->getId();
            $this->userRepository->save($user);
            $this->userRoleRepository->assignUserRole($userId, $role);
        });
    }
}
