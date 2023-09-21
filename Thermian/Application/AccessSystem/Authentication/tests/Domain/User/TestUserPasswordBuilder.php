<?php
declare(strict_types=1);

namespace Authentication\Test\Domain\User;

use Authentication\Domain\User\UserPassword;

class TestUserPasswordBuilder
{
    public static function random(): UserPassword
    {
        $characters = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));
        $emailLength = random_int(10, 20);

        $password = '';

        for ($i = 0; $i < $emailLength; $i++) {
            $password .= $characters[random_int(0, count($characters) - 1)];
        }

        return UserPassword::create($password);
    }
}
