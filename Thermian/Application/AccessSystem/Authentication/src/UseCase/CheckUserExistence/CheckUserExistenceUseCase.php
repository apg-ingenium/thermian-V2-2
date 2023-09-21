<?php
declare(strict_types=1);

namespace Authentication\UseCase\CheckUserExistence;

use Authentication\Domain\User\UserId;
use Authentication\Domain\UserRepository\UserRepository;

class CheckUserExistenceUseCase
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function execute(CheckUserExistenceQuery $query): bool
    {
        $userId = UserId::fromString($query->getUserId());

        return $this->userRepository->containsId($userId);
    }
}
