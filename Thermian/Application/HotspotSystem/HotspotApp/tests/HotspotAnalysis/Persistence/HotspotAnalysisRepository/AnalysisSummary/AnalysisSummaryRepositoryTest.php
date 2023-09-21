<?php
declare(strict_types=1);

namespace Hotspot\Test\HotspotAnalysis\Persistence\HotspotAnalysisRepository\AnalysisSummary;

use Cake\Database\Connection;
use Cake\Database\Driver\Mysql;
use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\AnalysisId;
use Hotspot\HotspotAnalysis\Domain\HotspotAnalysisRepository\DuplicateAnalysisIdException;
use Hotspot\HotspotAnalysis\Persistence\HotspotAnalysisRepository\AnalysisSummary\AnalysisSummaryRepository;
use PHPUnit\Framework\TestCase;

class AnalysisSummaryRepositoryTest extends TestCase
{
    private AnalysisSummaryRepository $repository;

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

        $this->repository = new AnalysisSummaryRepository($connection);
        $this->repository->removeAll();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->repository->removeAll();
    }

    public function testStoresHotspotAnalysisSummariesWithUniqueId(): void
    {
        $analysisId = AnalysisId::random();

        $analysisSummary =
            TestAnalysisSummaryBuilder
                ::random()
                ->withAnalysisId($analysisId)
                ->build();

        $this->repository->save($analysisSummary);
        $this->expectNotToPerformAssertions();
    }

    public function testDoesNotStoreAnalysisSummariesWithDuplicateIds(): void
    {
        $analysisId = AnalysisId::random();

        $analysisSummary =
            TestAnalysisSummaryBuilder::random()
                ->withAnalysisId($analysisId)
                ->build();

        $duplicateAnalysisSummary =
            TestAnalysisSummaryBuilder::random()
                ->withAnalysisId($analysisId)
                ->build();

        $this->repository->save($analysisSummary);

        try {
            $this->repository->save($duplicateAnalysisSummary);
            $this->fail();
        } catch (DuplicateAnalysisIdException) {
            $this->assertTrue($this->repository->containsId($analysisId));
            $storedAnalysisSummary = $this->repository->findById($analysisId);
            $this->assertEquals($analysisSummary, $storedAnalysisSummary);
        }
    }

    public function testRetrievesExistentHotspotAnalysisSummariesById(): void
    {
        $analysisId = AnalysisId::random();

        $analysisSummary =
            TestAnalysisSummaryBuilder
                ::random()
                ->withAnalysisId($analysisId)
                ->build();

        $this->repository->save($analysisSummary);
        $storedAnalysisSummary = $this->repository->findById($analysisId);

        $this->assertEquals($analysisSummary, $storedAnalysisSummary);
    }

    public function testDoesNotRetrieveNonExistentHotspotAnalysisSummariesById(): void
    {
        $analysisId = AnalysisId::random();
        $analysisSummary = $this->repository->findById($analysisId);
        $this->assertNull($analysisSummary);
    }

    public function testContainsTheCompositeIdOfExistentHotspotAnalysisSummaries(): void
    {
        $analysisId = AnalysisId::random();

        $analysisSummary =
            TestAnalysisSummaryBuilder
                ::random()
                ->withAnalysisId($analysisId)
                ->build();

        $this->repository->save($analysisSummary);

        $this->assertTrue($this->repository->containsId($analysisId));
    }

    public function testDoesNotContainTheIdOfNonExistentHotspotAnalysisSummaries(): void
    {
        $analysisId = AnalysisId::random();
        $this->assertFalse($this->repository->containsId($analysisId));
    }

    public function testRemovesExistentHotspotAnalysisSummariesById(): void
    {
        $analysisId = AnalysisId::random();

        $analysisSummary =
            TestAnalysisSummaryBuilder
                ::random()
                ->withAnalysisId($analysisId)
                ->build();

        $this->repository->save($analysisSummary);
        $this->repository->removeById($analysisId);

        $this->assertFalse($this->repository->containsId($analysisId));
        $this->assertNull($this->repository->findById($analysisId));
    }

    public function testRemovesNonExistentHotspotAnalysisSummariesById(): void
    {
        $analysisId = AnalysisId::random();
        $this->repository->removeById($analysisId);
        $this->assertFalse($this->repository->containsId($analysisId));
        $this->assertNull($this->repository->findById($analysisId));
    }

    public function testRemovesAllHotspotAnalysisSummaries(): void
    {
        $analysisSummaries = [
            TestAnalysisSummaryBuilder::random()->build(),
            TestAnalysisSummaryBuilder::random()->build(),
            TestAnalysisSummaryBuilder::random()->build(),
        ];

        foreach ($analysisSummaries as $analysisSummary) {
            $this->repository->save($analysisSummary);
        }

        $this->repository->removeAll();

        foreach ($analysisSummaries as $analysisSummary) {
            $analysisId = $analysisSummary->getId();
            $this->assertFalse($this->repository->containsId($analysisId));
            $this->assertNull($this->repository->findById($analysisId));
        }
    }
}
