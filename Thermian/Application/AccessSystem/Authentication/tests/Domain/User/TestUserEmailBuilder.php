<?php
declare(strict_types=1);

namespace Authentication\Test\Domain\User;

use Authentication\Domain\User\UserEmail;

class TestUserEmailBuilder
{
    public static function random(): UserEmail
    {
        $characters = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));
        $emailLength = random_int(10, 20);

        $email = '';

        for ($i = 0; $i < $emailLength; $i++) {
            $email .= $characters[random_int(0, count($characters) - 1)];
        }

        $email = $email . '@random.com';

        return UserEmail::create($email);
    }
}
