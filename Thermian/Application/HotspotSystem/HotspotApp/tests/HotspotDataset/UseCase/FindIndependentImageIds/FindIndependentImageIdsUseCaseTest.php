<?php
declare(strict_types=1);

namespace Hotspot\Test\HotspotDataset\UseCase\FindIndependentImageIds;

use Cake\Database\Connection;
use Cake\Database\Driver\Mysql;
use Hotspot\HotspotDataset\Domain\DatasetRepository\DatasetRepository;
use Hotspot\HotspotDataset\Domain\ImageRepository\ImageRepository;
use Hotspot\HotspotDataset\Persistence\DatasetRepository\MySQLDatasetRepository;
use Hotspot\HotspotDataset\Persistence\ImageRepository\MySQLImageRepository;
use Hotspot\HotspotDataset\UseCase\FindIndependentImageIds\FindIndependentImageIdsUseCase;
use Hotspot\Test\HotspotDataset\Domain\Image\TestImageBuilder;
use Hotspot\Test\HotspotDataset\UseCase\FindDatasetStats\DatasetStatsTestUtils;
use PHPUnit\Framework\TestCase;

class FindIndependentImageIdsUseCaseTest extends TestCase
{
    private FindIndependentImageIdsUseCase $useCase;
    private DatasetRepository $datasetRepository;
    private ImageRepository $imageRepository;
    private static DatasetStatsTestUtils $utils;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$utils = new DatasetStatsTestUtils();
    }

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

        $this->imageRepository = new MySQLImageRepository($connection);
        $this->datasetRepository = new MySQLDatasetRepository($connection, $this->imageRepository);
        $this->useCase = new FindIndependentImageIdsUseCase($this->datasetRepository);

        $this->tearDown();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->datasetRepository->removeAll();
        $this->imageRepository->removeAll();
    }

    public function testReturnsAnEmptyListWhenNoImageExists(): void
    {
        $imageIds = $this->useCase->execute();
        $this->assertEquals([], $imageIds);
    }

    public function testReturnsAnEmptyListWhenAllImagesBelongToDatasets(): void
    {
        $this->createDatasetWithStats(['numImages' => 3]);
        $imageIds = $this->useCase->execute();
        $this->assertEquals([], $imageIds);
    }

    public function testReturnsASingleIdWhenASingleIndependentImageExists(): void
    {
        $expectedIds = $this->createIndependentImages(1);
        $actualIds = $this->useCase->execute();
        $this->assertImageIdsMatch($expectedIds, $actualIds);
    }

    public function testReturnsMultipleIdsWhenMultipleIndependentImagesExist(): void
    {
        $expectedIds = $this->createIndependentImages(2);
        $actualIds = $this->useCase->execute();
        $this->assertImageIdsMatch($expectedIds, $actualIds);
    }

    public function testOnlyReturnsIndependentImageIdsWhenSomeImagesBelongToDatasets(): void
    {
        $expectedIds = $this->createIndependentImages(3);
        $this->createDatasetWithStats(['numImages' => 3]);
        $actualIds = $this->useCase->execute();
        $this->assertImageIdsMatch($expectedIds, $actualIds);
    }

    /** @return array<string> */
    private function createIndependentImages(int $numImages): array
    {
        $images = [];
        $imageIds = [];
        for ($index = 0; $index < $numImages; $index++) {
            $images[] = TestImageBuilder::random()->build();
            $imageIds[] = $images[$index]->getId()->value();
        }

        $this->imageRepository->saveAll($images);

        return $imageIds;
    }

    /** @param array<string, mixed> $stats */
    private function createDatasetWithStats(array $stats): void
    {
        $stats = self::$utils->createDatasetStats($stats);
        self::$utils->createDatasetWithStats($this->datasetRepository, $stats);
    }

    /**
     * @param array<string> $expected
     * @param array<string> $actual
     */
    private function assertImageIdsMatch(array $expected, array $actual): void
    {
        $expected = $this->orderIds($expected);
        $actual = $this->orderIds($actual);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @param array<string> $expected
     * @return array<bool>
     */
    private function orderIds(array $expected): array
    {
        $orderedIds = [];
        foreach ($expected as $id) {
            $orderedIds[$id] = true;
        }

        return $orderedIds;
    }
}
