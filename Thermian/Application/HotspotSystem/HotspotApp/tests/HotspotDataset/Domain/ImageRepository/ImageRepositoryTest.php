<?php
declare(strict_types=1);

namespace Hotspot\Test\HotspotDataset\Domain\ImageRepository;

use Hotspot\HotspotDataset\Domain\Image\ImageId;
use Hotspot\HotspotDataset\Domain\ImageRepository\DuplicateImageIdException;
use Hotspot\HotspotDataset\Domain\ImageRepository\ImageRepository;
use Hotspot\Test\HotspotDataset\Domain\Image\TestImageBuilder;
use PHPUnit\Framework\TestCase;

abstract class ImageRepositoryTest extends TestCase
{
    private ImageRepository $repository;

    abstract protected function getRepository(): ImageRepository;

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

    public function testStoresImagesWithUniqueId(): void
    {
        $image = TestImageBuilder::random()->build();
        $this->repository->save($image);
        $this->expectNotToPerformAssertions();
    }

    public function testDoesNotStoreImagesWithDuplicateIds(): void
    {
        $imageId = ImageId::random();
        $image = TestImageBuilder::random()->withId($imageId)->build();
        $duplicate = TestImageBuilder::random()->withId($imageId)->build();

        $this->repository->save($image);

        try {
            $this->repository->save($duplicate);
            $this->fail();
        } catch (DuplicateImageIdException) {
            $this->assertTrue($this->repository->containsId($imageId));
            $storedImage = $this->repository->findById($imageId);

            $this->assertNotNull($storedImage);
            assert(!is_null($storedImage)); # Static Analysis
            $this->assertObjectEquals($image, $storedImage);
        }
    }

    public function testRetrievesExistentImagesById(): void
    {
        $imageId = ImageId::random();
        $image = TestImageBuilder::random()->withId($imageId)->build();

        $this->repository->save($image);
        $storedImage = $this->repository->findById($imageId);

        $this->assertNotNull($storedImage);
        assert(!is_null($storedImage)); # Static Analysis
        $this->assertObjectEquals($image, $storedImage);
    }

    public function testDoesNotRetrieveNonExistentImagesById(): void
    {
        $image = $this->repository->findById(ImageId::random());
        $this->assertNull($image);
    }

    public function testContainsTheIdOfExistentImages(): void
    {
        $imageId = ImageId::random();
        $image = TestImageBuilder::random()->withId($imageId)->build();
        $this->repository->save($image);
        $this->assertTrue($this->repository->containsId($imageId));
    }

    public function testDoesNotContainTheIdOfNonExistentImages(): void
    {
        $this->assertFalse($this->repository->containsId(ImageId::random()));
    }

    public function testRemovesExistentImagesById(): void
    {
        $imageId = ImageId::random();
        $image = TestImageBuilder::random()->withId($imageId)->build();

        $this->repository->save($image);
        $this->repository->removeById($imageId);

        $this->assertFalse($this->repository->containsId($imageId));
        $this->assertNull($this->repository->findById($imageId));
    }

    public function testRemovesByNonExistentImagesById(): void
    {
        $imageId = ImageId::random();
        $this->repository->removeById($imageId);
        $this->assertFalse($this->repository->containsId($imageId));
        $this->assertNull($this->repository->findById($imageId));
    }

    public function testRemovesAllImages(): void
    {
        $images = [];

        for ($i = 0; $i < 3; $i++) {
            $image = TestImageBuilder::random()->build();
            $this->repository->save($image);
            $images[] = $image;
        }

        $this->repository->removeAll();

        foreach ($images as $image) {
            $imageId = $image->getId();
            $this->assertFalse($this->repository->containsId($imageId));
            $this->assertNull($this->repository->findById($imageId));
        }
    }
}
