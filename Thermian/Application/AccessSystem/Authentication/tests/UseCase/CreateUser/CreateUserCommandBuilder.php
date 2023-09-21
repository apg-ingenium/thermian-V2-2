<?php
declare(strict_types=1);

namespace Authentication\Test\UseCase\CreateUser;

use Authentication\Domain\User\User;
use Authentication\Domain\User\UserId;
use Authentication\Test\Domain\User\TestUserEmailBuilder;
use Authentication\Test\Domain\User\TestUserNameBuilder;
use Authentication\Test\Domain\User\TestUserPasswordBuilder;
use Authentication\UseCase\CreateUser\CreateUserCommand;

class CreateUserCommandBuilder
{
    private string $id;
    private string $name;
    private string $email;
    private string $password;

    public static function createUserCommand(): self
    {
        return new self();
    }

    private function __construct()
    {
        $this->id = UserId::random()->value();
        $this->name = TestUserNameBuilder::random()->value();
        $this->email = TestUserEmailBuilder::random()->value();
        $this->password = TestUserPasswordBuilder::random()->value();
    }

    public function withId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function withInvalidId(): self
    {
        $this->id = 'invalid';

        return $this;
    }

    public function withName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function withInvalidName(): self
    {
        $this->name = '';

        return $this;
    }

    public function withEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function withInvalidEmail(): self
    {
        $this->email = '';

        return $this;
    }

    public function withPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function withInvalidPassword(): self
    {
        $this->password = '';

        return $this;
    }

    public function forUser(User $user): self
    {
        $this->id = $user->getId()->value();
        $this->name = $user->getName()->value();
        $this->email = $user->getEmail()->value();
        $this->password = $user->getPassword()->value();

        return $this;
    }

    public function build(): CreateUserCommand
    {
        return new CreateUserCommand(
            $this->id,
            $this->name,
            $this->email,
            $this->password
        );
    }
}
