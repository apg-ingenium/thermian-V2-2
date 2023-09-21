<?php
declare(strict_types=1);

namespace Hotspot\Test\HotspotDataset\UseCase\DeleteDataset;

use Cake\Database\Connection;
use Cake\Database\Driver\Mysql;
use Hotspot\HotspotDataset\Domain\Dataset\InvalidDatasetIdException;
use Hotspot\HotspotDataset\Domain\DatasetRepository\DatasetRepository;
use Hotspot\HotspotDataset\Domain\Image\ImageId;
use Hotspot\HotspotDataset\Domain\ImageRepository\ImageRepository;
use Hotspot\HotspotDataset\Persistence\DatasetRepository\MySQLDatasetRepository;
use Hotspot\HotspotDataset\Persistence\ImageRepository\MySQLImageRepository;
use Hotspot\HotspotDataset\UseCase\DeleteDataset\DeleteDatasetCommand;
use Hotspot\HotspotDataset\UseCase\DeleteDataset\DeleteDatasetUseCase;
use Hotspot\HotspotDataset\UseCase\FindDatasetStats\FindDatasetStatsQuery;
use Hotspot\HotspotDataset\UseCase\FindDatasetStats\FindDatasetStatsUseCase;
use Hotspot\Test\HotspotDataset\UseCase\FindDatasetStats\DatasetStatsTestUtils;
use PHPUnit\Framework\TestCase;
use Shared\Domain\Uuid;

class DeleteDatasetUseCaseTest extends TestCase
{
    private static DatasetStatsTestUtils $utils;

    private DeleteDatasetUseCase $useCase;
    private FindDatasetStatsUseCase $findDatasetStatsUseCase;
    private DatasetRepository $datasetRepository;
    private ImageRepository $imageRepository;

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
        $this->findDatasetStatsUseCase = new FindDatasetStatsUseCase($this->datasetRepository);
        $this->useCase = new DeleteDatasetUseCase($this->datasetRepository);
        $this->tearDown();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->datasetRepository->removeAll();
        $this->imageRepository->removeAll();
    }

    public function testThrowsAnExceptionWhenAnInvalidDatasetIdIsProvided(): void
    {
        $this->expectException(InvalidDatasetIdException::class);
        $this->expectExceptionMessage('Invalid dataset id <dataset-id>');
        $this->deleteDataset('<dataset-id>');
    }

    public function testDeletesNonExistentDatasets(): void
    {
        $datasetId = Uuid::random()->value();
        $this->deleteDataset($datasetId);
        $this->expectNotToPerformAssertions();
    }

    public function testDeletesDatasetsWithNoImages(): void
    {
        $stats = $this->createDatasetWithStats(['numImages' => 0]);
        $id = $stats['id'];
        $this->deleteDataset($id);
        $this->assertDatasetDoesNotExist($id);
    }

    public function testDeletesDatasetsWithOneImage(): void
    {
        $stats = $this->createDatasetWithStats(['numImages' => 1]);
        $id = $stats['id'];
        $this->deleteDataset($id);
        $this->assertDatasetDoesNotExist($id);
    }

    public function testDeletesDatasetsWithMultipleImages(): void
    {
        $stats = $this->createDatasetWithStats(['numImages' => 2]);
        $id = $stats['id'];
        $this->deleteDataset($id);
        $this->assertDatasetDoesNotExist($id);
    }

    public function testDeletesDatasetImages(): void
    {
        $stats = $this->createDatasetWithStats(['numImages' => 3]);
        $this->deleteDataset($stats['id']);
        $this->assertImagesDoNotExist($stats['imageIds']);
    }

    public function testDoesNotDeleteOtherDatasetImages(): void
    {
        $stats1 = $this->createDatasetWithStats(['numImages' => 3]);
        $stats2 = $this->createDatasetWithStats(['numImages' => 3]);
        $this->deleteDataset($stats1['id']);
        $this->assertImagesExist($stats2['imageIds']);
    }

    public function testDoesNotModifyOtherDatasetStats(): void
    {
        $stats1 = $this->createDatasetWithStats(['numImages' => 3]);
        $stats2 = $this->createDatasetWithStats(['numImages' => 3]);
        $this->deleteDataset($stats1['id']);
        $stats2after = $this->findDatasetStats($stats2['id']);
        assert(!is_null($stats2after));
        $this->assertDatasetStatsMatch($stats2, $stats2after);
    }

    private function deleteDataset(string $datasetId): void
    {
        $this->useCase->execute(new DeleteDatasetCommand($datasetId));
    }

    /** @return array<string, mixed> */
    private function findDatasetStats(string $id): ?array
    {
        return $this->findDatasetStatsUseCase->execute(
            new FindDatasetStatsQuery($id)
        );
    }

    /**
     * @param array<string, mixed>|null $stats
     * @return array<string, mixed>
     */
    private function createDatasetWithStats(?array $stats): array
    {
        $stats = self::$utils->createDatasetStats($stats);
        self::$utils->createDatasetWithStats($this->datasetRepository, $stats);

        return $stats;
    }

    private function assertDatasetDoesNotExist(string $id): void
    {
        $id = Uuid::fromString($id);
        $this->assertFalse($this->datasetRepository->containsId($id));
    }

    /** @param array<string> $imageIds */
    private function assertImagesDoNotExist(array $imageIds): void
    {
        $imageIds = array_map(fn($id) => ImageId::fromString($id), $imageIds);
        $message = 'Failed asserting that the images do not exist';
        $this->assertFalse($this->imageRepository->containsAnyId($imageIds), $message);
    }

    /** @param array<string> $imageIds */
    private function assertImagesExist(array $imageIds): void
    {
        $imageIds = array_map(fn($id) => ImageId::fromString($id), $imageIds);
        $message = 'Failed asserting that the images exist';
        $this->assertTrue($this->imageRepository->containsAllIds($imageIds), $message);
    }

    /**
     * @param array<string, mixed> $expected
     * @param array<string, mixed> $actual
     */
    private function assertDatasetStatsMatch(array $expected, array $actual): void
    {
        self::$utils->assertDatasetStatsMatch([$expected], [$actual]);
    }
}
