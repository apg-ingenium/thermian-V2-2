<?php
declare(strict_types=1);

namespace Hotspot\Test\HotspotResults\Domain\HotspotCsvRepository;

use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\AnalysisId;
use Hotspot\HotspotDataset\Domain\Image\ImageId;
use Hotspot\HotspotResults\Domain\HotspotCsvRepository\HotspotCsvRepository;
use Hotspot\Test\HotspotResults\Domain\HotspotCsv\TestHotspotCsvBuilder;
use PHPUnit\Framework\TestCase;
use Shared\Domain\Uuid;
use Shared\Persistence\DuplicateIdException;

abstract class HotspotCsvRepositoryTest extends TestCase
{
    private HotspotCsvRepository $repository;

    abstract protected function getRepository(): HotspotCsvRepository;

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

    public function testStoresHotspotCsvResultsWithUniqueId(): void
    {
        $csvResults = TestHotspotCsvBuilder::random()->build();
        $this->repository->save($csvResults);
        $this->expectNotToPerformAssertions();
    }

    public function testDoesNotStoreHotspotCsvResultsWithDuplicateIds(): void
    {
        $id = Uuid::random();
        $csvResults = TestHotspotCsvBuilder::random()->withId($id)->build();
        $duplicateCsvResults = TestHotspotCsvBuilder::random()->withId($id)->build();
        $this->repository->save($csvResults);

        try {
            $this->repository->save($duplicateCsvResults);
            $this->fail();
        } catch (DuplicateIdException) {
            $this->assertTrue($this->repository->containsId($id));
            $storedCsv = $this->repository->findById($id);

            $this->assertNotNull($storedCsv);
            assert(!is_null($storedCsv)); # Static Analysis
            $this->assertObjectEquals($csvResults, $storedCsv);
        }
    }

    public function testStoresMultipleCsvResultsForAnExistentRecordId(): void
    {
        $analysisId = AnalysisId::random();
        $imageId = ImageId::random();

        $csv1 =
            TestHotspotCsvBuilder::random()
                ->withAnalysisId($analysisId)
                ->withImageId($imageId)
                ->build();

        $csv2 =
            TestHotspotCsvBuilder::random()
                ->withAnalysisId($analysisId)
                ->withImageId($imageId)
                ->build();

        $this->repository->save($csv1);
        $this->repository->save($csv2);

        $this->assertTrue($this->repository->containsRecordId($analysisId, $imageId));

        $this->assertTrue($this->repository->containsId($csv1->getId()));
        $storedCsv1 = $this->repository->findById($csv1->getId());
        assert(!is_null($storedCsv1)); # Static Analysis
        $this->assertObjectEquals($csv1, $storedCsv1);

        $this->assertTrue($this->repository->containsId($csv2->getId()));
        $storedCsv2 = $this->repository->findById($csv2->getId());
        assert(!is_null($storedCsv2)); # Static Analysis
        $this->assertObjectEquals($csv2, $storedCsv2);
    }

    public function testRetrievesOneCsvResultForAnExistentRecordId(): void
    {
        $analysisId = AnalysisId::random();
        $imageId = ImageId::random();
        $id = Uuid::random();

        $hotspotCsv =
            TestHotspotCsvBuilder::random()
                ->withAnalysisId($analysisId)
                ->withImageId($imageId)
                ->withId($id)
                ->build();

        $this->repository->save($hotspotCsv);
        $storedCsv = $this->repository->findByRecordId($analysisId, $imageId);
        $expectedCsvs = [$id->value() => $hotspotCsv];

        $this->assertEquals($expectedCsvs, $storedCsv);
    }

    public function testRetrievesMultipleCsvResultForAnExistentRecordId(): void
    {
        $analysisId = AnalysisId::random();
        $imageId = ImageId::random();

        $csv1 =
            TestHotspotCsvBuilder::random()
                ->withAnalysisId($analysisId)
                ->withImageId($imageId)
                ->build();

        $csv2 =
            TestHotspotCsvBuilder::random()
                ->withAnalysisId($analysisId)
                ->withImageId($imageId)
                ->build();

        $this->repository->save($csv1);
        $this->repository->save($csv2);
        $storedCsv = $this->repository->findByRecordId($analysisId, $imageId);

        $expectedCsvs = [
            $csv1->getId()->value() => $csv1,
            $csv2->getId()->value() => $csv2,
        ];

        $this->assertEquals($expectedCsvs, $storedCsv);
    }

    public function testDoesNotRetrieveCsvResultsForNonExistentRecordIds(): void
    {
        $analysisId = AnalysisId::random();
        $imageId = ImageId::random();
        $hotspotCsv = $this->repository->findByRecordId($analysisId, $imageId);
        $this->assertEmpty($hotspotCsv);
    }

    public function testContainsCsvResultsForExistentRecordIds(): void
    {
        $analysisId = AnalysisId::random();
        $imageId = ImageId::random();

        $hotspotCsv =
            TestHotspotCsvBuilder::random()
                ->withAnalysisId($analysisId)
                ->withImageId($imageId)
                ->build();

        $this->repository->save($hotspotCsv);

        $this->assertTrue($this->repository->containsRecordId($analysisId, $imageId));
    }

    public function testDoesNotContainCsvResultsForNonExistentRecordIds(): void
    {
        $analysisId = AnalysisId::random();
        $imageId = ImageId::random();
        $this->assertFalse($this->repository->containsRecordId($analysisId, $imageId));
    }

    public function testRemovesCsvResultsForExistentRecordIds(): void
    {
        $analysisId = AnalysisId::random();
        $imageId = ImageId::random();

        $hotspotCsv =
            TestHotspotCsvBuilder::random()
                ->withAnalysisId($analysisId)
                ->withImageId($imageId)
                ->build();

        $this->repository->save($hotspotCsv);
        $this->repository->removeByRecordId($analysisId, $imageId);

        $this->assertFalse($this->repository->containsRecordId($analysisId, $imageId));
        $this->assertEmpty($this->repository->findByRecordId($analysisId, $imageId));
    }

    public function testRemovesCsvResultsForNonExistentRecordIds(): void
    {
        $analysisId = AnalysisId::random();
        $imageId = ImageId::random();
        $this->repository->removeByRecordId($analysisId, $imageId);
        $this->assertFalse($this->repository->containsRecordId($analysisId, $imageId));
        $this->assertEmpty($this->repository->findByRecordId($analysisId, $imageId));
    }

    public function testRemovesAllCsvResults(): void
    {
        $csvResults = [];

        for ($i = 0; $i < 3; $i++) {
            $csv = TestHotspotCsvBuilder::random()->build();
            $this->repository->save($csv);
            $csvResults[] = $csv;
        }

        $this->repository->removeAll();

        foreach ($csvResults as $csv) {
            $this->assertFalse($this->repository->containsId($csv->getId()));
            $this->assertNull($this->repository->findById($csv->getId()));
        }
    }
}
