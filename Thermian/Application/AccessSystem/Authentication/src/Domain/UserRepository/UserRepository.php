<?php
declare(strict_types=1);

namespace Authentication\Domain\UserRepository;

use Authentication\Domain\User\User;
use Authentication\Domain\User\UserEmail;
use Authentication\Domain\User\UserId;

interface UserRepository
{
    public function save(User $user): void;

    public function containsId(UserId $userId): bool;

    public function findById(UserId $userId): ?User;

    public function findByEmail(UserEmail $email): ?User;

    public function removeById(UserId $userId): void;

    public function removeAll(): void;
}
