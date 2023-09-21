<?php
declare(strict_types=1);

namespace Hotspot\Test\HotspotDataset\UseCase\FindDatasetStats;

use Cake\Database\Connection;
use Cake\Database\Driver\Mysql;
use Hotspot\HotspotDataset\Domain\DatasetRepository\DatasetRepository;
use Hotspot\HotspotDataset\Domain\Image\ImageId;
use Hotspot\HotspotDataset\Domain\ImageRepository\ImageRepository;
use Hotspot\HotspotDataset\Persistence\DatasetRepository\MySQLDatasetRepository;
use Hotspot\HotspotDataset\Persistence\ImageRepository\MySQLImageRepository;
use Hotspot\HotspotDataset\UseCase\FindDatasetStats\FindAllDatasetStatsUseCase;
use Hotspot\Test\HotspotDataset\Domain\Image\TestImageBuilder;
use PHPUnit\Framework\TestCase;

class FindAllDatasetStatsUseCaseTest extends TestCase
{
    private FindAllDatasetStatsUseCase $useCase;
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
        $this->useCase = new FindAllDatasetStatsUseCase($this->datasetRepository);

        $this->tearDown();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->datasetRepository->removeAll();
        $this->imageRepository->removeAll();
    }

    public function testReturnsAnEmptyListWhenNoDatasetExists(): void
    {
        $datasetStats = $this->useCase->execute();
        $this->assertEquals([], $datasetStats);
    }

    public function testReturnsDatasetStatsWhenASingleDatasetWithNoImagesExists(): void
    {
        $stats = $this->createDatasetStats(['numImages' => 0]);
        $this->createDatasetWithStats($stats);

        $actualStats = $this->useCase->execute();

        $this->assertDatasetStatsMatch([$stats], $actualStats);
    }

    public function testReturnsDatasetStatsWhenASingleDatasetWithOneImageExists(): void
    {
        $stats = $this->createDatasetStats(['numImages' => 1]);
        $this->createDatasetWithStats($stats);

        $actualStats = $this->useCase->execute();

        $this->assertDatasetStatsMatch([$stats], $actualStats);
    }

    public function testReturnsDatasetStatsWhenASingleDatasetWithMultipleImagesExists(): void
    {
        $stats = $this->createDatasetStats(['numImages' => 2]);
        $this->createDatasetWithStats($stats);

        $actualStats = $this->useCase->execute();

        $this->assertDatasetStatsMatch([$stats], $actualStats);
    }

    public function testReturnsDatasetStatsWhenMultipleDatasetsExist(): void
    {
        $numStats = 3;
        $expectedStats = [];
        foreach (range(1, $numStats) as $index) {
            $stats = $this->createDatasetStats();
            $this->createDatasetWithStats($stats);
            $expectedStats[$index] = $stats;
        }

        $actualStats = $this->useCase->execute();

        $this->assertDatasetStatsMatch($expectedStats, $actualStats);
    }

    public function testDoesNotIncludeIndependentImageStats(): void
    {
        $imageId = ImageId::random();
        $this->createIndependentImageWithId($imageId);

        $stats = $this->createDatasetStats(['numImages' => 3]);
        $this->createDatasetWithStats($stats);

        $actualStats = $this->useCase->execute();
        $datasetImageIds = $actualStats[0]['imageIds'];

        $this->assertNotContains($imageId->value(), $datasetImageIds);
    }

    /**
     * @param array<string, mixed>|null $stats
     * @return array<string, mixed>
     */
    private function createDatasetStats(array|null $stats = null): array
    {
        return self::$utils->createDatasetStats($stats);
    }

    /** @param array<string, mixed> $stats */
    private function createDatasetWithStats(array &$stats): void
    {
        self::$utils->createDatasetWithStats($this->datasetRepository, $stats);
    }

    private function createIndependentImageWithId(ImageId $id): void
    {
        $this->imageRepository->save(TestImageBuilder::random()->withId($id)->build());
    }

    /**
     * @param array<int, array<string, mixed>> $expected
     * @param array<int, array<string, mixed>> $actual
     */
    private function assertDatasetStatsMatch(array $expected, array $actual): void
    {
        self::$utils->assertDatasetStatsMatch($expected, $actual);
    }
}
