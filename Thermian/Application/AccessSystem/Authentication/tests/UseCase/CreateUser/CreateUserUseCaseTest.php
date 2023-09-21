<?php
declare(strict_types=1);

namespace Authentication\Test\UseCase\CreateUser;

use Authentication\Domain\User\InvalidUserEmailException;
use Authentication\Domain\User\InvalidUserNameException;
use Authentication\Domain\User\InvalidUserPasswordException;
use Authentication\Domain\User\UserEmail;
use Authentication\Domain\User\UserId;
use Authentication\Domain\UserRepository\DuplicateUserEmailException;
use Authentication\Domain\UserRepository\DuplicateUserIdException;
use Authentication\Domain\UserRepository\UserRepository;
use Authentication\Test\Domain\User\TestUserBuilder;
use Authentication\UseCase\CreateUser\CreateUserTransaction;
use Authentication\UseCase\CreateUser\CreateUserUseCase;
use Authorization\Domain\Role\Role;
use Authorization\Domain\UserRoleRepository\UserRoleRepository;
use Cake\Database\Connection;
use Cake\Database\Driver\Mysql;
use Hamcrest\Matchers;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Shared\Domain\InvalidUuidException;

class CreateUserUseCaseTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private CreateUserUseCase $useCase;
    private MockInterface $userRepository;
    private MockInterface $userRoleRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $connection = new Connection([
            'driver' => new Mysql([
                'host' => env('MYSQL_HOST'),
                'username' => env('MYSQL_USER'),
                'password' => env('MYSQL_PASSWORD'),
                'database' => env('MYSQL_DATABASE'),
            ]),
        ]);

        $this->userRepository = Mockery::mock(
            UserRepository::class
        );
        $this->userRepository->shouldIgnoreMissing();

        $this->userRoleRepository = Mockery::mock(
            UserRoleRepository::class
        );
        $this->userRoleRepository->shouldIgnoreMissing();

        $this->useCase = new CreateUserUseCase(
            new CreateUserTransaction(
                $this->userRepository,
                $this->userRoleRepository,
                $connection
            )
        );
    }

    public function testCreatesUsersWithValidCredentials(): void
    {
        $user = TestUserBuilder::random()->build();

        $command = CreateUserCommandBuilder
            ::createUserCommand()
            ->forUser($user)
            ->build();

        $this->userRepository
            ->shouldReceive('save')
            ->with(Mockery::capture($createdUser))
            ->once();

        $this->useCase->execute($command);

        $this->assertObjectEquals($user, $createdUser);
    }

    public function testCreatesUsersWithTheAnalystRole(): void
    {
        $user = TestUserBuilder::random()->build();
        $role = Role::of('analyst');

        $command = CreateUserCommandBuilder
            ::createUserCommand()
            ->forUser($user)
            ->build();

        $this->userRoleRepository
            ->shouldReceive('assignUserRole')
            ->with(
                Matchers::equalTo($user->getId()),
                Matchers::equalTo($role)
            )
            ->once();

        $this->useCase->execute($command);
    }

    public function testDoesNotCreateUsersWithInvalidIds(): void
    {
        $command = CreateUserCommandBuilder
            ::createUserCommand()
            ->withInvalidId()
            ->build();

        try {
            $this->useCase->execute($command);
            $this->fail();
        } catch (InvalidUuidException) {
            $this->userRepository->shouldNotHaveBeenCalled();
        }
    }

    public function testDoesNotCreateUsersWithInvalidNames(): void
    {
        $command = CreateUserCommandBuilder
            ::createUserCommand()
            ->withInvalidName()
            ->build();

        try {
            $this->useCase->execute($command);
            $this->fail();
        } catch (InvalidUserNameException) {
            $this->userRepository->shouldNotHaveBeenCalled();
        }
    }

    public function testDoesNotCreateUsersWithInvalidEmails(): void
    {
        $command = CreateUserCommandBuilder
            ::createUserCommand()
            ->withInvalidEmail()
            ->build();

        try {
            $this->useCase->execute($command);
            $this->fail();
        } catch (InvalidUserEmailException) {
            $this->userRepository->shouldNotHaveBeenCalled();
        }
    }

    public function testDoesNotCreateUsersWithInvalidPasswords(): void
    {
        $command = CreateUserCommandBuilder
            ::createUserCommand()
            ->withInvalidPassword()
            ->build();

        try {
            $this->useCase->execute($command);
            $this->fail();
        } catch (InvalidUserPasswordException) {
            $this->userRepository->shouldNotHaveBeenCalled();
        }
    }

    public function testDoesNotCreateUsersWithDuplicateIds(): void
    {
        $command = CreateUserCommandBuilder
            ::createUserCommand()
            ->build();

        $userId = UserId::fromString($command->getId());
        $exception = DuplicateUserIdException::forId($userId);

        $this->userRepository
            ->shouldReceive('save')
            ->andThrow($exception)
            ->once();

        $this->expectException(DuplicateUserIdException::class);
        $this->useCase->execute($command);
    }

    public function testDoesNotCreateUsersWithDuplicateEmails(): void
    {
        $command = CreateUserCommandBuilder
            ::createUserCommand()
            ->build();

        $email = UserEmail::create($command->getEmail());
        $exception = DuplicateUserEmailException::forEmail($email);

        $this->userRepository
            ->shouldReceive('save')
            ->andThrow($exception)
            ->once();

        $this->expectException(DuplicateUserEmailException::class);
        $this->useCase->execute($command);
    }
}
