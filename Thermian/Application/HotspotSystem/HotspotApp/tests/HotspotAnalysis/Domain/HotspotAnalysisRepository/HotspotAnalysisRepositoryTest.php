<?php
declare(strict_types=1);

namespace Hotspot\Test\HotspotAnalysis\Domain\HotspotAnalysisRepository;

use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\AnalysisId;
use Hotspot\HotspotAnalysis\Domain\HotspotAnalysisRepository\DuplicateAnalysisIdException;
use Hotspot\HotspotAnalysis\Domain\HotspotAnalysisRepository\HotspotAnalysisRepository;
use Hotspot\HotspotDataset\Domain\Image\ImageId;
use Hotspot\Test\HotspotAnalysis\Domain\HotspotAnalysis\TestHotspotAnalysisRecordBuilder;
use PHPUnit\Framework\TestCase;

abstract class HotspotAnalysisRepositoryTest extends TestCase
{
    private HotspotAnalysisRepository $repository;

    abstract protected function getRepository(): HotspotAnalysisRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->getRepository();
        $this->repository->removeAll();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->repository->removeAll();
    }

    public function testStoresHotspotAnalysisRecordsWithUniqueIds(): void
    {
        $analysisId = AnalysisId::random();
        $imageId = ImageId::random();

        $record = TestHotspotAnalysisRecordBuilder
            ::hotspotAnalysisRecord()
            ->withAnalysisId($analysisId)
            ->withImageId($imageId)
            ->withRandomHotspots()
            ->build();

        $this->repository->saveAnalysisRecord($record);

        $this->expectNotToPerformAssertions();
    }

    public function testDoesNotStoreHotspotAnalysisRecordsWithDuplicateIds(): void
    {
        $analysisId = AnalysisId::random();
        $imageId = ImageId::random();

        $record = TestHotspotAnalysisRecordBuilder
            ::hotspotAnalysisRecord()
            ->withAnalysisId($analysisId)
            ->withImageId($imageId)
            ->withRandomHotspots()
            ->build();

        $duplicateRecord = TestHotspotAnalysisRecordBuilder
            ::hotspotAnalysisRecord()
            ->withAnalysisId($analysisId)
            ->withImageId($imageId)
            ->withRandomHotspots()
            ->build();

        $this->repository->saveAnalysisRecord($record);

        try {
            $this->repository->saveAnalysisRecord($duplicateRecord);
            $this->fail();
        } catch (DuplicateAnalysisIdException) {
            $this->assertTrue($this->repository->containsAnalysisRecordId($analysisId, $imageId));
            $storedRecord = $this->repository->findAnalysisRecordById($analysisId, $imageId);

            $this->assertNotNull($storedRecord);
            assert(!is_null($storedRecord)); # Static Analysis
            $this->assertObjectEquals($record, $storedRecord);
        }
    }

    public function testRetrievesExistentHotspotAnalysisRecordsById(): void
    {
        $analysisId = AnalysisId::random();
        $imageId = ImageId::random();

        $record = TestHotspotAnalysisRecordBuilder
            ::hotspotAnalysisRecord()
            ->withAnalysisId($analysisId)
            ->withImageId($imageId)
            ->withRandomHotspots()
            ->build();

        $this->repository->saveAnalysisRecord($record);

        $storedRecord = $this->repository
            ->findAnalysisRecordById($analysisId, $imageId);

        $this->assertNotNull($storedRecord);
        assert(!is_null($storedRecord)); # Static Analysis
        $this->assertObjectEquals($record, $storedRecord);
    }

    public function testDoesNotRetrieveNonExistentHotspotAnalysisRecordsById(): void
    {
        $analysisId = AnalysisId::random();
        $imageId = ImageId::random();
        $record = $this->repository->findAnalysisRecordById($analysisId, $imageId);
        $this->assertNull($record);
    }

    public function testContainsTheIdOfExistentHotspotAnalysisRecords(): void
    {
        $analysisId = AnalysisId::random();
        $imageId = ImageId::random();

        $record = TestHotspotAnalysisRecordBuilder
            ::hotspotAnalysisRecord()
            ->withAnalysisId($analysisId)
            ->withImageId($imageId)
            ->withRandomHotspots()
            ->build();

        $this->repository->saveAnalysisRecord($record);

        $this->assertTrue($this->repository->containsAnalysisRecordId($analysisId, $imageId));
    }

    public function testDoesNotContainTheIdOfNonExistentHotspotAnalysisRecords(): void
    {
        $analysisId = AnalysisId::random();
        $imageId = ImageId::random();
        $this->assertFalse($this->repository->containsAnalysisRecordId($analysisId, $imageId));
    }

    public function testRemovesExistentHotspotAnalysesRecordsById(): void
    {
        $analysisId = AnalysisId::random();
        $imageId = ImageId::random();

        $record = TestHotspotAnalysisRecordBuilder
            ::hotspotAnalysisRecord()
            ->withAnalysisId($analysisId)
            ->withImageId($imageId)
            ->withRandomHotspots()
            ->build();

        $this->repository->saveAnalysisRecord($record);
        $this->repository->removeAnalysisRecordById($analysisId, $imageId);

        $this->assertFalse($this->repository->containsAnalysisRecordId($analysisId, $imageId));
        $this->assertNull($this->repository->findAnalysisRecordById($analysisId, $imageId));
    }

    public function testRemovesNonExistentHotspotAnalysisRecordsById(): void
    {
        $analysisId = AnalysisId::random();
        $imageId = ImageId::random();
        $this->repository->removeAnalysisRecordById($analysisId, $imageId);
        $this->assertFalse($this->repository->containsAnalysisRecordId($analysisId, $imageId));
        $this->assertNull($this->repository->findAnalysisRecordById($analysisId, $imageId));
    }

    public function testRemovesAllHotspotAnalyses(): void
    {
        $records = [
            TestHotspotAnalysisRecordBuilder::hotspotAnalysisRecord()->withRandomHotspots()->build(),
            TestHotspotAnalysisRecordBuilder::hotspotAnalysisRecord()->withRandomHotspots()->build(),
            TestHotspotAnalysisRecordBuilder::hotspotAnalysisRecord()->withRandomHotspots()->build(),
        ];

        foreach ($records as $record) {
            $this->repository->saveAnalysisRecord($record);
        }

        $this->repository->removeAll();

        foreach ($records as $record) {
            $analysisId = $record->getAnalysisId();
            $imageId = $record->getImageId();
            $this->assertFalse($this->repository->containsAnalysisRecordId($analysisId, $imageId));
            $this->assertNull($this->repository->findAnalysisRecordById($analysisId, $imageId));
        }
    }
}
