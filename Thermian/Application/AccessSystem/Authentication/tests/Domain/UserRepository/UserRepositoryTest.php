<?php
declare(strict_types=1);

namespace Authentication\Test\Domain\UserRepository;

use Authentication\Domain\User\UserId;
use Authentication\Domain\UserRepository\DuplicateUserEmailException;
use Authentication\Domain\UserRepository\DuplicateUserIdException;
use Authentication\Domain\UserRepository\UserRepository;
use Authentication\Test\Domain\User\TestUserBuilder;
use Authentication\Test\Domain\User\TestUserEmailBuilder;
use PHPUnit\Framework\TestCase;

abstract class UserRepositoryTest extends TestCase
{
    private UserRepository $repository;

    abstract protected function getRepository(): UserRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->getRepository();
        $this->tearDown();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->repository->removeAll();
    }

    public function testStoresUsersWithUniqueId(): void
    {
        $user = TestUserBuilder::random()->build();
        $this->repository->save($user);
        $this->expectNotToPerformAssertions();
    }

    public function testDoesNotSaveUsersWithDuplicateIds(): void
    {
        $userId = UserId::random();
        $user = TestUserBuilder::random()->withId($userId)->build();
        $duplicate = TestUserBuilder::random()->withId($userId)->build();

        $this->repository->save($user);

        try {
            $this->repository->save($duplicate);
            $this->fail();
        } catch (DuplicateUserIdException) {
            $this->assertTrue($this->repository->containsId($userId));
            $storedUser = $this->repository->findById($userId);

            $this->assertNotNull($storedUser);
            assert(!is_null($storedUser)); # Static Analysis
            $this->assertObjectEquals($user, $storedUser);
        }
    }

    public function testDoesNotSaveUsersWithDuplicateEmails(): void
    {
        $email = TestUserEmailBuilder::random();
        $user = TestUserBuilder::random()->withEmail($email)->build();
        $duplicate = TestUserBuilder::random()->withEmail($email)->build();

        $this->repository->save($user);

        try {
            $this->repository->save($duplicate);
            $this->fail();
        } catch (DuplicateUserEmailException) {
            $this->assertTrue($this->repository->containsId($user->getId()));
            $storedUser = $this->repository->findById($user->getId());

            $this->assertNotNull($storedUser);
            assert(!is_null($storedUser)); # Static Analysis
            $this->assertObjectEquals($user, $storedUser);
        }
    }

    public function testRetrievesByIdStoredUsers(): void
    {
        $userId = UserId::random();
        $user = TestUserBuilder::random()->withId($userId)->build();

        $this->repository->save($user);
        $storedUser = $this->repository->findById($userId);

        $this->assertNotNull($storedUser);
        assert(!is_null($storedUser)); # Static Analysis
        $this->assertObjectEquals($user, $storedUser);
    }

    public function testDoesNotRetrieveByIdUsersThatWereNotStored(): void
    {
        $user = $this->repository->findById(UserId::random());
        $this->assertNull($user);
    }

    public function testRetrievesStoredUsersByEmail(): void
    {
        $email = TestUserEmailBuilder::random();
        $user = TestUserBuilder::random()->withEmail($email)->build();

        $this->repository->save($user);
        $storedUser = $this->repository->findByEmail($email);

        $this->assertNotNull($storedUser);
        assert(!is_null($storedUser)); # Static Analysis
        $this->assertObjectEquals($user, $storedUser);
    }

    public function testDoesNotRetrievesNonStoredByEmail(): void
    {
        $email = TestUserEmailBuilder::random();
        $user = $this->repository->findByEmail($email);
        $this->assertNull($user);
    }

    public function testContainsTheIdOfUsersThatWereStored(): void
    {
        $userId = UserId::random();
        $user = TestUserBuilder::random()->withId($userId)->build();
        $this->repository->save($user);
        $this->assertTrue($this->repository->containsId($userId));
    }

    public function testDoesNotContainTheIdOfUsersThatWereNotStored(): void
    {
        $this->assertFalse($this->repository->containsId(UserId::random()));
    }

    public function testRemovesByIdStoredUsers(): void
    {
        $userId = UserId::random();
        $user = TestUserBuilder::random()->withId($userId)->build();

        $this->repository->save($user);
        $this->repository->removeById($userId);

        $this->assertFalse($this->repository->containsId($userId));
        $this->assertNull($this->repository->findById($userId));
    }

    public function testRemovesByIdUsersThatWereNotStored(): void
    {
        $userId = UserId::random();
        $this->repository->removeById($userId);
        $this->assertFalse($this->repository->containsId($userId));
        $this->assertNull($this->repository->findById($userId));
    }

    public function testRemovesAllUsers(): void
    {
        $users = [];

        for ($i = 0; $i < 3; $i++) {
            $user = TestUserBuilder::random()->build();
            $this->repository->save($user);
            $users[] = $user;
        }

        $this->repository->removeAll();

        foreach ($users as $user) {
            $userId = $user->getId();
            $this->assertFalse($this->repository->containsId($userId));
            $this->assertNull($this->repository->findById($userId));
        }
    }
}
