<?php
declare(strict_types=1);

namespace Authentication\Test\Domain\User;

use Authentication\Domain\User\UserName;

class TestUserNameBuilder
{
    public static function random(): UserName
    {
        $characters = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));
        $emailLength = random_int(10, 20);

        $name = '';

        for ($i = 0; $i < $emailLength; $i++) {
            $name .= $characters[random_int(0, count($characters) - 1)];
        }

        return UserName::create($name);
    }
}
