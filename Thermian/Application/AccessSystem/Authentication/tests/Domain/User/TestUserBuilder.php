<?php
declare(strict_types=1);

namespace Authentication\Test\Domain\User;

use Authentication\Domain\User\User;
use Authentication\Domain\User\UserEmail;
use Authentication\Domain\User\UserId;
use Authentication\Domain\User\UserName;
use Authentication\Domain\User\UserPassword;

class TestUserBuilder
{
    public static function random(): self
    {
        return new TestUserBuilder();
    }

    private UserId $id;
    private UserName $name;
    private UserEmail $email;
    private UserPassword $password;

    private function __construct()
    {
        $this->id = UserId::random();
        $this->name = TestUserNameBuilder::random();
        $this->email = TestUserEmailBuilder::random();
        $this->password = TestUserPasswordBuilder::random();
    }

    public function withId(UserId $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function withName(UserName $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function withEmail(UserEmail $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function withPassword(UserPassword $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function build(): User
    {
        return User::create($this->id, $this->name, $this->email, $this->password);
    }
}
