<?php
declare(strict_types=1);

namespace Authorization\Test\Domain\UserRoleRepository;

use Authentication\Domain\User\UserId;
use Authentication\Domain\UserRepository\UserRepository;
use Authentication\Test\Domain\User\TestUserBuilder;
use Authorization\Domain\Role\Role;
use Authorization\Domain\RoleRepository\RoleRepository;
use Authorization\Domain\UserRoleRepository\NonExistentRoleException;
use Authorization\Domain\UserRoleRepository\NonExistentUserException;
use Authorization\Domain\UserRoleRepository\RemovalOfRoleInUseException;
use Authorization\Domain\UserRoleRepository\UserAlreadyHasRoleException;
use Authorization\Domain\UserRoleRepository\UserRoleRepository;
use PHPUnit\Framework\TestCase;

abstract class UserRoleRepositoryTest extends TestCase
{
    private UserRoleRepository $repository;
    private RoleRepository $roleRepository;
    private UserRepository $userRepository;

    abstract protected function getRepository(): UserRoleRepository;

    abstract protected static function getRoleRepository(): RoleRepository;

    abstract protected function getUserRepository(): UserRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->getRepository();
        $this->roleRepository = $this->getRoleRepository();
        $this->userRepository = $this->getUserRepository();
        $this->tearDown();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->repository->removeAllUserRoles();
        $this->roleRepository->removeAll();
        $this->userRepository->removeAll();
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        static::getRoleRepository()->reset();
    }

    public function testDoesNotRemoveRolesThatAreInUse(): void
    {
        $role = Role::of('admin');
        $user = TestUserBuilder::random()->build();

        $this->roleRepository->create($role);
        $this->userRepository->save($user);
        $this->repository->assignUserRole($user->getId(), $role);

        try {
            $this->roleRepository->remove($role);
            $this->fail();
        } catch (RemovalOfRoleInUseException) {
            $this->assertTrue($this->roleRepository->contains($role));
        }
    }

    public function testRemoveAllRolesDoesNotRemoveAnyRoleWhenARoleIsInUse(): void
    {
        $roles = [Role::of('admin'), Role::of('analyst')];
        $user = TestUserBuilder::random()->build();

        foreach ($roles as $role) {
            $this->roleRepository->create($role);
        }

        $this->userRepository->save($user);
        $this->repository->assignUserRole($user->getId(), Role::of('admin'));

        try {
            $this->roleRepository->removeAll();
            $this->fail();
        } catch (RemovalOfRoleInUseException) {
            foreach ($roles as $role) {
                $this->assertTrue($this->roleRepository->contains($role));
            }
        }
    }

    public function testAssignsExistentRolesToExistentUsers(): void
    {
        $role = Role::of('admin');
        $user = TestUserBuilder::random()->build();

        $this->userRepository->save($user);
        $this->roleRepository->create($role);
        $this->repository->assignUserRole($user->getId(), $role);

        $this->expectNotToPerformAssertions();
    }

    public function testDoesNotAssignRolesToUsersWhoAlreadyHaveARole(): void
    {
        $oldRole = Role::of('admin');
        $newRole = Role::of('analyst');
        $user = TestUserBuilder::random()->build();

        $this->userRepository->save($user);
        $this->roleRepository->create($oldRole);
        $this->roleRepository->create($newRole);
        $this->repository->assignUserRole($user->getId(), $oldRole);

        try {
            $this->repository->assignUserRole($user->getId(), $newRole);
            $this->fail();
        } catch (UserAlreadyHasRoleException) {
            $this->assertFalse($this->repository->containsUserRole($user->getId(), $newRole));
            $this->assertTrue($this->repository->containsUserRole($user->getId(), $oldRole));
            $foundRole = $this->repository->findUserRole($user->getId());
            assert(!is_null($foundRole));
            $this->assertObjectEquals($oldRole, $foundRole);
        }
    }

    public function testDoesNotAssignRolesToNonExistentUsers(): void
    {
        $role = Role::of('admin');
        $userId = UserId::random();

        $this->roleRepository->create($role);

        try {
            $this->repository->assignUserRole($userId, $role);
            $this->fail();
        } catch (NonExistentUserException) {
            $this->assertFalse($this->repository->containsUserRole($userId, $role));
        }
    }

    public function testDoesNotAssignNonExistentRolesToExistentUsers(): void
    {
        $role = Role::of('admin');
        $user = TestUserBuilder::random()->build();

        try {
            $this->repository->assignUserRole($user->getId(), $role);
            $this->fail();
        } catch (NonExistentRoleException) {
            $this->assertFalse($this->repository->containsUserRole($user->getId(), $role));
        }
    }

    public function testContainsUserRolesOnceAssigned(): void
    {
        $role = Role::of('admin');
        $user = TestUserBuilder::random()->build();

        $this->userRepository->save($user);
        $this->roleRepository->create($role);
        $this->repository->assignUserRole($user->getId(), $role);

        $this->assertTrue($this->repository->containsUserRole($user->getId(), $role));
    }

    public function testDoesNotContainsUserRolesThatWereNotAssigned(): void
    {
        $role = Role::of('admin');
        $userId = UserId::random();
        $this->assertFalse($this->repository->containsUserRole($userId, $role));
    }

    public function testFindsTheRoleOfExistingUsers(): void
    {
        $role = Role::of('admin');
        $user = TestUserBuilder::random()->build();

        $this->userRepository->save($user);
        $this->roleRepository->create($role);
        $this->repository->assignUserRole($user->getId(), $role);

        $foundRole = $this->repository->findUserRole($user->getId());
        assert($foundRole !== null);
        $this->assertObjectEquals($role, $foundRole);
    }

    public function testDoesNotFindTheRoleOfNonExistentUsers(): void
    {
        $this->assertNull($this->repository->findUserRole(UserId::random()));
    }

    public function testDoesNotFindTheRoleOfExistentUsersWithoutRole(): void
    {
        $user = TestUserBuilder::random()->build();
        $this->userRepository->save($user);
        $this->assertNull($this->repository->findUserRole($user->getId()));
    }

    public function testUpdatesTheRolesOfExistentUsersToExistentRoles(): void
    {
        $role = Role::of('admin');
        $newRole = Role::of('Analyst');
        $user = TestUserBuilder::random()->build();

        $this->userRepository->save($user);
        $this->roleRepository->create($role);
        $this->roleRepository->create($newRole);
        $this->repository->assignUserRole($user->getId(), $role);
        $this->repository->updateUserRole($user->getId(), $newRole);

        $foundRole = $this->repository->findUserRole($user->getId());
        assert($foundRole !== null);
        $this->assertObjectEquals($newRole, $foundRole);
    }

    public function testDoesNotUpdateTheRolesOfExistentUsersToNonExistentRoles(): void
    {
        $oldRole = Role::of('admin');
        $newRole = Role::of('analyst');
        $user = TestUserBuilder::random()->build();

        $this->userRepository->save($user);
        $this->roleRepository->create($oldRole);
        $this->repository->assignUserRole($user->getId(), $oldRole);

        try {
            $this->repository->updateUserRole($user->getId(), $newRole);
            $this->fail();
        } catch (NonExistentRoleException) {
            $foundRole = $this->repository->findUserRole($user->getId());
            assert($foundRole !== null);
            $this->assertObjectEquals($oldRole, $foundRole);
        }
    }

    public function testDoesNotUpdateTheRolesOfNonExistentUsers(): void
    {
        $oldRole = Role::of('admin');
        $newRole = Role::of('analyst');
        $user = TestUserBuilder::random()->build();

        $this->roleRepository->create($oldRole);
        $this->roleRepository->create($newRole);

        try {
            $this->repository->updateUserRole($user->getId(), $newRole);
            $this->fail();
        } catch (NonExistentUserException) {
            $this->assertFalse($this->repository->containsUserRole($user->getId(), $newRole));
            $this->assertNull($this->repository->findUserRole($user->getId()));
        }
    }

    public function testRemovesTheRoleOfExistentUsersWithRole(): void
    {
        $role = Role::of('admin');
        $user = TestUserBuilder::random()->build();

        $this->userRepository->save($user);
        $this->roleRepository->create($role);
        $this->repository->assignUserRole($user->getId(), $role);

        $this->repository->removeUserRole($user->getId());
        $this->assertFalse($this->repository->containsUserRole($user->getId(), $role));
        $this->assertNull($this->repository->findUserRole($user->getId()));
    }

    public function testRemovesTheRoleOfExistentUsersWithoutRole(): void
    {
        $role = Role::of('admin');
        $user = TestUserBuilder::random()->build();
        $this->userRepository->save($user);
        $this->roleRepository->create($role);
        $this->repository->removeUserRole($user->getId());
        $this->expectNotToPerformAssertions();
    }

    public function testRemovesTheRoleOfNonExistentUsers(): void
    {
        $role = Role::of('admin');
        $userId = UserId::random();
        $this->roleRepository->create($role);
        $this->repository->removeUserRole($userId);
        $this->expectNotToPerformAssertions();
    }

    public function testRemovesAllUserRoleAssignments(): void
    {
        $adminRole = Role::of('admin');
        $admin = TestUserBuilder::random()->build();

        $analystRole = Role::of('analyst');
        $analyst = TestUserBuilder::random()->build();

        $this->userRepository->save($admin);
        $this->userRepository->save($analyst);
        $this->roleRepository->create($adminRole);
        $this->roleRepository->create($analystRole);

        $this->repository->removeAllUserRoles();

        $this->assertFalse($this->repository->containsUserRole($admin->getId(), $adminRole));
        $this->assertFalse($this->repository->containsUserRole($analyst->getId(), $analystRole));
        $this->assertNull($this->repository->findUserRole($admin->getId()));
        $this->assertNull($this->repository->findUserRole($analyst->getId()));
    }
}
