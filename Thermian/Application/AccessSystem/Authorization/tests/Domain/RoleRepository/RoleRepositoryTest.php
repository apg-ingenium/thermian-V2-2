<?php
declare(strict_types=1);

namespace Authorization\Test\Domain\RoleRepository;

use Authorization\Domain\Role\Role;
use Authorization\Domain\RoleRepository\DuplicateRoleException;
use Authorization\Domain\RoleRepository\RoleRepository;
use PHPUnit\Framework\TestCase;

abstract class RoleRepositoryTest extends TestCase
{
    private RoleRepository $repository;

    abstract protected static function getRepository(): RoleRepository;

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

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        static::getRepository()->reset();
    }

    public function testCreatesUniqueRoles(): void
    {
        $role = Role::of('admin');
        $this->repository->create($role);
        $this->expectNotToPerformAssertions();
    }

    public function testDoesNotCreateDuplicateRoles(): void
    {
        $role = Role::of('admin');
        $this->repository->create($role);

        try {
            $this->repository->create($role);
            $this->fail();
        } catch (DuplicateRoleException) {
            $this->assertTrue($this->repository->contains($role));
        }
    }

    public function testContainsExistentRoles(): void
    {
        $role = Role::of('admin');
        $this->repository->create($role);
        $this->assertTrue($this->repository->contains($role));
    }

    public function testDoesNotContainNonExistentRoles(): void
    {
        $role = Role::of('admin');
        $this->assertFalse($this->repository->contains($role));
    }

    public function testListsAllExistentRoles(): void
    {
        $roles = [Role::of('admin'), Role::of('analyst')];
        $this->repository->create($roles[0]);
        $this->repository->create($roles[1]);
        $foundRoles = $this->repository->listAll();
        $this->assertEqualsCanonicalizing($roles, $foundRoles);
    }

    public function testRemovesExistentRoles(): void
    {
        $role = Role::of('admin');
        $this->repository->create($role);
        $this->repository->remove($role);
        $this->assertFalse($this->repository->contains($role));
    }

    public function testRemovesNonExistentRoles(): void
    {
        $role = Role::of('admin');
        $this->repository->remove($role);
        $this->assertFalse($this->repository->contains($role));
    }

    public function testRemovesAllExistentRoles(): void
    {
        $roles = [Role::of('admin'), Role::of('analyst')];

        foreach ($roles as $role) {
            $this->repository->create($role);
        }

        $this->repository->removeAll();

        foreach ($roles as $role) {
            $this->assertFalse($this->repository->contains($role));
        }
    }
}
