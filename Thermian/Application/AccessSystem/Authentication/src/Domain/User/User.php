<?php
declare(strict_types=1);

namespace Authentication\Domain\User;

class User
{
    public static function create(UserId $id, UserName $name, UserEmail $email, UserPassword $password): self
    {
        return new User($id, $name, $email, $password);
    }

    private UserId $id;
    private UserName $name;
    private UserEmail $email;
    private UserPassword $password;

    private function __construct(UserId $id, UserName $name, UserEmail $email, UserPassword $password)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
    }

    public function getId(): UserId
    {
        return $this->id;
    }

    public function getName(): UserName
    {
        return $this->name;
    }

    public function getEmail(): UserEmail
    {
        return $this->email;
    }

    public function getPassword(): UserPassword
    {
        return $this->password;
    }

    public function equals(User $other): bool
    {
        return $this->id->equals($other->id)
            && $this->name->equals($other->name)
            && $this->email->equals($other->email)
            && $this->password->equals($other->password);
    }
}
