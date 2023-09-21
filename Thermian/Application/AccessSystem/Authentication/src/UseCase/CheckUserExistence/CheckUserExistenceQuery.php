<?php
declare(strict_types=1);

namespace Authentication\UseCase\CheckUserExistence;

class CheckUserExistenceQuery
{
    private string $userId;

    public function __construct(string $userId)
    {
        $this->userId = $userId;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }
}
