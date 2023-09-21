<?php
declare(strict_types=1);

namespace Authentication\UseCase\AuthenticateUser;

use Authentication\Domain\User\UserEmail;
use Authentication\Domain\User\UserPassword;
use Authentication\Domain\UserRepository\UserRepository;

class AuthenticateUserUseCase
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function execute(AuthenticateUserCommand $command): void
    {
        $email = UserEmail::create($command->getEmail());
        $password = UserPassword::create($command->getPassword());

        $user = $this->userRepository->findByEmail($email);

        if (is_null($user)) {
            throw UserAuthenticationException::invalidCredentials();
        }

        $userPassword = $user->getPassword();

        if (!$userPassword->equals($password)) {
            throw UserAuthenticationException::invalidCredentials();
        }
    }
}
