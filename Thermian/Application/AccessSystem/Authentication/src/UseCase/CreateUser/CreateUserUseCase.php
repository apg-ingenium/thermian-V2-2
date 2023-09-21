<?php
declare(strict_types=1);

namespace Authentication\UseCase\CreateUser;

use Authentication\Domain\User\User;
use Authentication\Domain\User\UserEmail;
use Authentication\Domain\User\UserId;
use Authentication\Domain\User\UserName;
use Authentication\Domain\User\UserPassword;
use Authorization\Domain\Role\Role;

class CreateUserUseCase
{
    private CreateUserTransaction $createUserTransaction;

    public function __construct(
        CreateUserTransaction $createUserTransaction
    ) {
        $this->createUserTransaction = $createUserTransaction;
    }

    public function execute(CreateUserCommand $command): void
    {
        $userId = UserId::fromString($command->getId());
        $name = UserName::create($command->getName());
        $email = UserEmail::create($command->getEmail());
        $password = UserPassword::create($command->getPassword());

        $user = User::create($userId, $name, $email, $password);
        $role = Role::of('analyst');

        $this->createUserTransaction->execute($user, $role);
    }
}
