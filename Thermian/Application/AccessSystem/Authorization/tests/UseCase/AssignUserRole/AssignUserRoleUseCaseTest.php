<?php
declare(strict_types=1);

namespace Authorization\Test\UseCase\AssignUserRole;

use Authentication\Domain\User\UserId;
use Authorization\Domain\Role\InvalidRoleException;
use Authorization\Domain\Role\Role;
use Authorization\Domain\UserRoleRepository\NonExistentRoleException;
use Authorization\Domain\UserRoleRepository\NonExistentUserException;
use Authorization\Domain\UserRoleRepository\UserAlreadyHasRoleException;
use Authorization\Domain\UserRoleRepository\UserRoleRepository;
use Authorization\UseCase\AssignUserRole\AssignUserRoleUseCase;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Shared\Domain\InvalidUuidException;

class AssignUserRoleUseCaseTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private AssignUserRoleUseCase $useCase;
    private MockInterface $userRoleRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRoleRepository = Mockery::mock(
            UserRoleRepository::class
        );
        $this->useCase = new AssignUserRoleUseCase(
            $this->userRoleRepository,
        );
    }

    public function testAssignsRolesToExistingUsers(): void
    {
        $command = AssignUserRoleCommandBuilder
            ::assignUserRoleCommand()
            ->build();

        $this->userRoleRepository
            ->shouldReceive('assignUserRole')
            ->with(Mockery::capture($userId), Mockery::capture($role))
            ->once();

        $this->useCase->execute($command);

        $this->assertEquals($command->getUserId(), $userId->value());
        $this->assertEquals($command->getRole(), $role->value());
    }

    public function testDoesNotAssignValidRolesToUsersWithInvalidIds(): void
    {
        $command = AssignUserRoleCommandBuilder
            ::assignUserRoleCommand()
            ->toUserWithInvalidId()
            ->build();

        try {
            $this->useCase->execute($command);
            $this->fail();
        } catch (InvalidUuidException) {
            $this->userRoleRepository->shouldNotHaveBeenCalled();
        }
    }

    public function testDoesNotAssignInvalidRolesToUsersWithValidIds(): void
    {
        $command = AssignUserRoleCommandBuilder
            ::assignUserRoleCommand()
            ->withInvalidRole()
            ->build();

        try {
            $this->useCase->execute($command);
            $this->fail();
        } catch (InvalidRoleException) {
            $this->userRoleRepository->shouldNotHaveBeenCalled();
        }
    }

    public function testDoesNotAssignNonExistentRolesToExistingUsers(): void
    {
        $command = AssignUserRoleCommandBuilder
            ::assignUserRoleCommand()
            ->build();

        $role = Role::of($command->getRole());
        $exception = NonExistentRoleException::forRole($role);

        $this->userRoleRepository
            ->shouldReceive('assignUserRole')
            ->withAnyArgs()
            ->andThrow($exception)
            ->once();

        $this->expectException(NonExistentRoleException::class);
        $this->useCase->execute($command);
    }

    public function testDoesNotAssignExistentRolesToNonExistentUsers(): void
    {
        $command = AssignUserRoleCommandBuilder
            ::assignUserRoleCommand()
            ->build();

        $userId = UserId::fromString($command->getUserId());
        $exception = NonExistentUserException::withId($userId);

        $this->userRoleRepository
            ->shouldReceive('assignUserRole')
            ->withAnyArgs()
            ->andThrow($exception)
            ->once();

        $this->expectException(NonExistentUserException::class);
        $this->useCase->execute($command);
    }

    public function testDoesNotAssignRolesToUsersWhoAlreadyHaveARole(): void
    {
        $command = AssignUserRoleCommandBuilder
            ::assignUserRoleCommand()
            ->build();

        $userId = UserId::fromString($command->getUserId());
        $exception = UserAlreadyHasRoleException::forUser($userId);

        $this->userRoleRepository
            ->shouldReceive('assignUserRole')
            ->withAnyArgs()
            ->andThrow($exception)
            ->once();

        $this->expectException(UserAlreadyHasRoleException::class);
        $this->useCase->execute($command);
    }
}
