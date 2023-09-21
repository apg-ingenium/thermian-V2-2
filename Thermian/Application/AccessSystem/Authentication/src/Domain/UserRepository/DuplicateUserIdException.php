<?php
declare(strict_types=1);

namespace Authentication\Domain\UserRepository;

use Authentication\Domain\User\UserId;
use Shared\Persistence\DuplicateIdException;

class DuplicateUserIdException extends DuplicateIdException
{
    public static function forId(UserId $id): self
    {
        return new DuplicateUserIdException($id);
    }

    private function __construct(UserId $id)
    {
        parent::__construct("Duplicate user Id {$id->value()}");
    }
}
