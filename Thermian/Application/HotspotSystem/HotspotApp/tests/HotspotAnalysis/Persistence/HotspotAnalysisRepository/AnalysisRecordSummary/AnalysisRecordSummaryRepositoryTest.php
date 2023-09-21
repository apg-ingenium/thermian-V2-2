<?php
declare(strict_types=1);

namespace Hotspot\Test\HotspotAnalysis\Persistence\HotspotAnalysisRepository\AnalysisRecordSummary;

use Cake\Database\Connection;
use Cake\Database\Driver\Mysql;
use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\AnalysisId;
use Hotspot\HotspotAnalysis\Domain\HotspotAnalysisRepository\DuplicateAnalysisIdException;
use Hotspot\HotspotAnalysis\Persistence\HotspotAnalysisRepository\AnalysisRecordSummary\AnalysisRecordSummaryRepository;
use Hotspot\HotspotAnalysis\Persistence\HotspotAnalysisRepository\AnalysisSummary\AnalysisSummaryRepository;
use Hotspot\HotspotDataset\Domain\Image\ImageId;
use Hotspot\Test\HotspotAnalysis\Persistence\HotspotAnalysisRepository\AnalysisSummary\TestAnalysisSummaryBuilder;
use PHPUnit\Framework\TestCase;

class AnalysisRecordSummaryRepositoryTest extends TestCase
{
    private AnalysisRecordSummaryRepository $repository;
    private AnalysisSummaryRepository $analysisSummaryRepository;

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
        $this->repository = new AnalysisRecordSummaryRepository($connection);
        $this->analysisSummaryRepository = new AnalysisSummaryRepository($connection);
        $this->tearDown();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->repository->removeAll();
        $this->analysisSummaryRepository->removeAll();
    }

    public function testStoresRecordSummariesWithUniqueRecordId(): void
    {
        $analysisId = AnalysisId::random();

        $analysisSummary =
            TestAnalysisSummaryBuilder
                ::random()
                ->withAnalysisId($analysisId)
                ->build();

        $recordSummary =
            TestAnalysisRecordSummaryBuilder
                ::random()
                ->withAnalysisId($analysisId)
                ->build();

        $this->analysisSummaryRepository->save($analysisSummary);
        $this->repository->save($recordSummary);
        $this->expectNotToPerformAssertions();
    }

    public function testDoesNotStoreRecordsSummariesWithDuplicateRecordIds(): void
    {
        $analysisId = AnalysisId::random();
        $imageId = ImageId::random();

        $analysisSummary =
            TestAnalysisSummaryBuilder
                ::random()
                ->withAnalysisId($analysisId)
                ->build();

        $recordSummary =
            TestAnalysisRecordSummaryBuilder::random()
                ->withAnalysisId($analysisId)
                ->withImageId($imageId)
                ->build();

        $duplicateRecordSummary =
            TestAnalysisRecordSummaryBuilder::random()
                ->withAnalysisId($analysisId)
                ->withImageId($imageId)
                ->build();

        $this->analysisSummaryRepository->save($analysisSummary);
        $this->repository->save($recordSummary);

        try {
            $this->repository->save($duplicateRecordSummary);
            $this->fail();
        } catch (DuplicateAnalysisIdException) {
            $this->assertTrue($this->repository->containsAnalysisRecordId($analysisId, $imageId));
            $storedRecordSummary = $this->repository->findByRecordId($analysisId, $imageId);
            $this->assertEquals($recordSummary, $storedRecordSummary);
        }
    }

    public function testRetrievesExistentRecordSummariesByRecordId(): void
    {
        $analysisId = AnalysisId::random();
        $imageId = ImageId::random();

        $analysisSummary =
            TestAnalysisSummaryBuilder
                ::random()
                ->withAnalysisId($analysisId)
                ->build();

        $recordSummary =
            TestAnalysisRecordSummaryBuilder
                ::random()
                ->withAnalysisId($analysisId)
                ->withImageId($imageId)
                ->build();

        $this->analysisSummaryRepository->save($analysisSummary);
        $this->repository->save($recordSummary);
        $storedRecordSummary = $this->repository->findByRecordId($analysisId, $imageId);

        $this->assertEquals($recordSummary, $storedRecordSummary);
    }

    public function testDoesNotRetrieveNonExistentRecordSummariesByRecordId(): void
    {
        $analysisId = AnalysisId::random();
        $imageId = ImageId::random();
        $imageAnalysis = $this->repository->findByRecordId($analysisId, $imageId);
        $this->assertNull($imageAnalysis);
    }

    public function testContainsTheRecordIdOfExistentRecordSummaries(): void
    {
        $analysisId = AnalysisId::random();
        $imageId = ImageId::random();

        $analysisSummary =
            TestAnalysisSummaryBuilder
                ::random()
                ->withAnalysisId($analysisId)
                ->build();

        $recordSummary =
            TestAnalysisRecordSummaryBuilder
                ::random()
                ->withAnalysisId($analysisId)
                ->withImageId($imageId)
                ->build();

        $this->analysisSummaryRepository->save($analysisSummary);
        $this->repository->save($recordSummary);

        $this->assertTrue($this->repository->containsAnalysisRecordId($analysisId, $imageId));
    }

    public function testDoesNotContainTheRecordIdOfNonExistentRecordSummaries(): void
    {
        $analysisId = AnalysisId::random();
        $imageId = ImageId::random();
        $this->assertFalse($this->repository->containsAnalysisRecordId($analysisId, $imageId));
    }

    public function testRemovesExistentRecordSummariesByRecordId(): void
    {
        $analysisId = AnalysisId::random();
        $imageId = ImageId::random();

        $analysisSummary =
            TestAnalysisSummaryBuilder
                ::random()
                ->withAnalysisId($analysisId)
                ->withNumRecords(3)
                ->withNumPanels(10)
                ->withNumHotspots(100)
                ->build();

        $recordSummary =
            TestAnalysisRecordSummaryBuilder
                ::random()
                ->withAnalysisId($analysisId)
                ->withImageId($imageId)
                ->withNumPanels(3)
                ->withNumHotspots(20)
                ->build();

        $this->analysisSummaryRepository->save($analysisSummary);
        $this->repository->save($recordSummary);
        $this->repository->removeByAnalysisRecordId($analysisId, $imageId);

        $this->assertFalse($this->repository->containsAnalysisRecordId($analysisId, $imageId));
        $this->assertNull($this->repository->findByRecordId($analysisId, $imageId));

        $updatedAnalysisDetails = $this->analysisSummaryRepository->findById($analysisId);
        $this->assertNotNull($updatedAnalysisDetails);
        assert(!is_null($updatedAnalysisDetails)); # static analysis
        $this->assertEquals(2, $updatedAnalysisDetails->getNumRecords());
        $this->assertEquals(7, $updatedAnalysisDetails->getNumPanels());
        $this->assertEquals(80, $updatedAnalysisDetails->getNumHotspots());
    }

    public function testRemovesAnalysisSummariesWhenTheLastRecordSummaryIsRemoved(): void
    {
        $analysisId = AnalysisId::random();
        $imageId = ImageId::random();

        $analysisSummary =
            TestAnalysisSummaryBuilder
                ::random()
                ->withAnalysisId($analysisId)
                ->withNumRecords(1)
                ->withNumPanels(10)
                ->withNumHotspots(100)
                ->build();

        $recordSummary =
            TestAnalysisRecordSummaryBuilder
                ::random()
                ->withAnalysisId($analysisId)
                ->withImageId($imageId)
                ->withNumPanels(3)
                ->withNumHotspots(20)
                ->build();

        $this->analysisSummaryRepository->save($analysisSummary);
        $this->repository->save($recordSummary);
        $this->repository->removeByAnalysisRecordId($analysisId, $imageId);

        $this->assertFalse($this->repository->containsAnalysisRecordId($analysisId, $imageId));
        $this->assertNull($this->repository->findByRecordId($analysisId, $imageId));

        $updatedAnalysisDetails = $this->analysisSummaryRepository->findById($analysisId);
        $this->assertFalse($this->analysisSummaryRepository->containsId($analysisId));
        $this->assertNull($updatedAnalysisDetails);
    }

    public function testRemovesNonExistentRecordSummariesByRecordId(): void
    {
        $analysisId = AnalysisId::random();
        $imageId = ImageId::random();
        $this->repository->removeByAnalysisId($analysisId);
        $this->assertFalse($this->repository->containsAnalysisRecordId($analysisId, $imageId));
        $this->assertNull($this->repository->findByRecordId($analysisId, $imageId));
    }

    public function testRemovesExistentRecordSummariesByAnalysisId(): void
    {
        $analysisId = AnalysisId::random();
        $imageId1 = ImageId::random();
        $imageId2 = ImageId::random();

        $analysisSummary =
            TestAnalysisSummaryBuilder
                ::random()
                ->withAnalysisId($analysisId)
                ->withNumPanels(10)
                ->withNumHotspots(100)
                ->build();

        $recordSummary1 =
            TestAnalysisRecordSummaryBuilder
                ::random()
                ->withAnalysisId($analysisId)
                ->withImageId($imageId1)
                ->withNumPanels(3)
                ->withNumHotspots(20)
                ->build();

        $recordSummary2 =
            TestAnalysisRecordSummaryBuilder
                ::random()
                ->withAnalysisId($analysisId)
                ->withImageId($imageId2)
                ->withNumPanels(7)
                ->withNumHotspots(80)
                ->build();

        $this->analysisSummaryRepository->save($analysisSummary);
        $this->repository->save($recordSummary1);
        $this->repository->save($recordSummary2);

        $this->repository->removeByAnalysisId($analysisId);

        $this->assertFalse($this->repository->containsAnalysisRecordId($analysisId, $imageId1));
        $this->assertNull($this->repository->findByRecordId($analysisId, $imageId1));

        $this->assertFalse($this->repository->containsAnalysisRecordId($analysisId, $imageId2));
        $this->assertNull($this->repository->findByRecordId($analysisId, $imageId2));

        $updatedAnalysisDetails = $this->analysisSummaryRepository->findById($analysisId);
        $this->assertNull($updatedAnalysisDetails);
    }

    public function testRemovesNonExistentRecordSummariesByAnalysisId(): void
    {
        $analysisId = AnalysisId::random();
        $this->repository->removeByAnalysisId($analysisId);
        $this->assertEmpty($this->repository->findByAnalysisId($analysisId));
    }

    public function testRemovesAllRecordSummaries(): void
    {
        $analysisId = AnalysisId::random();

        $analysisSummary =
            TestAnalysisSummaryBuilder
                ::random()
                ->withAnalysisId($analysisId)
                ->build();

        $recordSummaries = [
            TestAnalysisRecordSummaryBuilder::random()->withAnalysisId($analysisId)->build(),
            TestAnalysisRecordSummaryBuilder::random()->withAnalysisId($analysisId)->build(),
            TestAnalysisRecordSummaryBuilder::random()->withAnalysisId($analysisId)->build(),
        ];

        $this->analysisSummaryRepository->save($analysisSummary);

        foreach ($recordSummaries as $recordSummary) {
            $this->repository->save($recordSummary);
        }

        $this->repository->removeAll();

        foreach ($recordSummaries as $recordSummary) {
            $imageId = $recordSummary->getImageId();
            $this->assertFalse($this->repository->containsAnalysisRecordId($analysisId, $imageId));
            $this->assertNull($this->repository->findByRecordId($analysisId, $imageId));
        }
    }
}
