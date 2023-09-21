<?php
declare(strict_types=1);

namespace Hotspot\Test\HotspotAnalysis\Persistence\HotspotRepository;

use Cake\Database\Connection;
use Cake\Database\Driver\Mysql;
use Hotspot\HotspotAnalysis\Domain\Hotspot\HotspotId;
use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\AnalysisId;
use Hotspot\HotspotAnalysis\Domain\HotspotAnalysisRepository\DuplicateHotspotIdException;
use Hotspot\HotspotAnalysis\Domain\HotspotRepository\HotspotRepository;
use Hotspot\HotspotAnalysis\Domain\PanelRepository\PanelRepository;
use Hotspot\HotspotAnalysis\Persistence\HotspotAnalysisRepository\AnalysisRecordSummary\AnalysisRecordSummaryRepository;
use Hotspot\HotspotAnalysis\Persistence\HotspotAnalysisRepository\AnalysisSummary\AnalysisSummaryRepository;
use Hotspot\HotspotAnalysis\Persistence\HotspotRepository\MySQLHotspotRepository;
use Hotspot\HotspotAnalysis\Persistence\PanelRepository\MySQLPanelRepository;
use Hotspot\HotspotDataset\Domain\Image\ImageId;
use Hotspot\Test\HotspotAnalysis\Persistence\HotspotAnalysisRepository\AnalysisRecordSummary\TestAnalysisRecordSummaryBuilder;
use Hotspot\Test\HotspotAnalysis\Persistence\HotspotAnalysisRepository\AnalysisSummary\TestAnalysisSummaryBuilder;
use Hotspot\Test\HotspotAnalysis\Persistence\HotspotRepository\HotspotEntity\TestHotspotEntityBuilder;
use Hotspot\Test\HotspotAnalysis\Persistence\PanelRepository\PanelEntity\TestPanelEntityBuilder;
use PHPUnit\Framework\TestCase;
use Shared\Domain\Uuid;
use function env;

class MySQLHotspotRepositoryTest extends TestCase
{
    private HotspotRepository $repository;
    private AnalysisSummaryRepository $analysisDetailsRepository;
    private AnalysisRecordSummaryRepository $imageAnalysisRepository;
    private PanelRepository $panelRepository;
    private AnalysisId $analysisId;
    private ImageId $imageId;
    private Uuid $panelId;

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
        $this->repository = new MySQLHotspotRepository($connection);
        $this->analysisDetailsRepository = new AnalysisSummaryRepository($connection);
        $this->imageAnalysisRepository = new AnalysisRecordSummaryRepository($connection);
        $this->panelRepository = new MySQLPanelRepository($connection);
        $this->tearDown();

        $this->analysisId = AnalysisId::random();
        $this->imageId = ImageId::random();
        $this->panelId = Uuid::random();

        $analysisDetails =
            TestAnalysisSummaryBuilder::random()
                ->withAnalysisId($this->analysisId)
                ->build();

        $imageAnalysis =
            TestAnalysisRecordSummaryBuilder::random()
                ->withAnalysisId($this->analysisId)
                ->withImageId($this->imageId)
                ->build();

        $panel =
            TestPanelEntityBuilder::random()
                ->withId($this->panelId)
                ->withAnalysisId($this->analysisId)
                ->withImageId($this->imageId)
                ->build();

        $this->analysisDetailsRepository->save($analysisDetails);
        $this->imageAnalysisRepository->save($imageAnalysis);
        $this->panelRepository->save($panel);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->repository->removeAll();
        $this->panelRepository->removeAll();
        $this->imageAnalysisRepository->removeAll();
        $this->analysisDetailsRepository->removeAll();
    }

    public function testStoresHotspotsWithUniqueId(): void
    {
        $hotspotId = HotspotId::random();

        $hotspot =
            TestHotspotEntityBuilder::random()
                ->withId($hotspotId)
                ->withPanelId($this->panelId)
                ->build();

        $this->repository->save($hotspot);
        $this->expectNotToPerformAssertions();
    }

    public function testStoresMultipleHotspotsWithUniqueIds(): void
    {
        $hotspotCollection = [];

        for ($i = 0; $i < 3; $i++) {
            $hotspotCollection[] =
                TestHotspotEntityBuilder::random()
                    ->withPanelId($this->panelId)
                    ->build();
        }

        $this->repository->saveAll($hotspotCollection);
        $this->expectNotToPerformAssertions();
    }

    public function testRetrievesMultipleExistentHotspotsById(): void
    {
        $hotspotCollection = [];

        for ($i = 0; $i < 3; $i++) {
            $hotspotCollection[] =
                TestHotspotEntityBuilder::random()
                    ->withPanelId($this->panelId)
                    ->build();
        }

        $this->repository->saveAll($hotspotCollection);

        foreach ($hotspotCollection as $hotspot) {
            $storedHotspot = $this->repository->findById($hotspot->getId());
            $this->assertNotNull($storedHotspot);
            $this->assertEquals($hotspot, $storedHotspot);
        }
    }

    public function testDoesNonExistentHotspotsWithDuplicateIds(): void
    {
        $hotspotId = HotspotId::random();

        $hotspot =
            TestHotspotEntityBuilder::random()
                ->withId($hotspotId)
                ->withPanelId($this->panelId)
                ->build();

        $duplicateHotspot =
            TestHotspotEntityBuilder::random()
                ->withId($hotspotId)
                ->withPanelId($this->panelId)
                ->build();

        $this->repository->save($hotspot);

        try {
            $this->repository->save($duplicateHotspot);
            $this->fail();
        } catch (DuplicateHotspotIdException) {
            $this->assertTrue($this->repository->containsId($hotspotId));
            $storedHotspot = $this->repository->findById($hotspotId);

            $this->assertNotNull($storedHotspot);
            $this->assertEquals($hotspot, $storedHotspot);
        }
    }

    public function testDoesNotStoreMultipleHotspotsWithDuplicateIds(): void
    {
        $uniqueId = HotspotId::random();
        $duplicateId = HotspotId::random();

        $originalHotspot =
            TestHotspotEntityBuilder::random()
                ->withId($duplicateId)
                ->withPanelId($this->panelId)
                ->build();

        $hotspotCollection = [
            TestHotspotEntityBuilder::random()
                ->withId($duplicateId)
                ->withPanelId($this->panelId)
                ->build(),
            TestHotspotEntityBuilder::random()
                ->withId($uniqueId)
                ->withPanelId($this->panelId)
                ->build(),
        ];

        $this->repository->save($originalHotspot);

        try {
            $this->repository->saveAll($hotspotCollection);
            $this->fail();
        } catch (DuplicateHotspotIdException) {
            $this->assertTrue($this->repository->containsId($duplicateId));
            $storedHotspot = $this->repository->findById($duplicateId);
            $this->assertNotNull($storedHotspot);
            $this->assertEquals($originalHotspot, $storedHotspot);

            $this->assertFalse($this->repository->containsId($uniqueId));
            $this->assertNull($this->repository->findById($uniqueId));
        }
    }

    public function testRetrievesExistentHotspotsById(): void
    {
        $hotspotId = HotspotId::random();

        $hotspot =
            TestHotspotEntityBuilder::random()
                ->withId($hotspotId)
                ->withPanelId($this->panelId)
                ->build();

        $this->repository->save($hotspot);
        $storedHotspot = $this->repository->findById($hotspotId);

        $this->assertNotNull($storedHotspot);
        $this->assertEquals($hotspot, $storedHotspot);
    }

    public function testDoesNotRetrieveNonExistentHotspotsById(): void
    {
        $hotspotId = HotspotId::random();
        $hotspot = $this->repository->findById($hotspotId);
        $this->assertNull($hotspot);
    }

    public function testContainsTheIdOfExistentHotspots(): void
    {
        $hotspotId = HotspotId::random();

        $hotspot =
            TestHotspotEntityBuilder::random()
                ->withId($hotspotId)
                ->withPanelId($this->panelId)
                ->build();

        $this->repository->save($hotspot);

        $this->assertTrue($this->repository->containsId($hotspotId));
    }

    public function testDoesNotContainTheIdOfNonExistentHotspots(): void
    {
        $this->assertFalse($this->repository->containsId(HotspotId::random()));
    }

    public function testRemovesExistentHotspotsById(): void
    {
        $hotspotId = HotspotId::random();

        $hotspot =
            TestHotspotEntityBuilder::random()
                ->withId($hotspotId)
                ->withPanelId($this->panelId)
                ->build();

        $this->repository->save($hotspot);
        $this->repository->removeById($hotspotId);

        $this->assertFalse($this->repository->containsId($hotspotId));
        $this->assertNull($this->repository->findById($hotspotId));
    }

    public function testRemovesNonExistentHotspotsById(): void
    {
        $hotspotId = HotspotId::random();
        $this->repository->removeById($hotspotId);
        $this->assertFalse($this->repository->containsId($hotspotId));
        $this->assertNull($this->repository->findById($hotspotId));
    }

    public function testRemovesAllHotspots(): void
    {
        $hotspots = [];

        for ($i = 0; $i < 3; $i++) {
            $hotspot =
                TestHotspotEntityBuilder::random()
                    ->withPanelId($this->panelId)
                    ->build();
            $this->repository->save($hotspot);
            $hotspots[] = $hotspot;
        }

        $this->repository->removeAll();

        foreach ($hotspots as $hotspot) {
            $hotspotId = $hotspot->getId();
            $this->assertFalse($this->repository->containsId($hotspotId));
            $this->assertNull($this->repository->findById($hotspotId));
        }
    }
}
