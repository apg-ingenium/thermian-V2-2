<?php
declare(strict_types=1);

namespace Hotspot\Test\HotspotDataset\UseCase\FindDatasetStats;

use DateTime;
use Hotspot\HotspotDataset\Domain\Dataset\DatasetName;
use Hotspot\HotspotDataset\Domain\Dataset\InMemoryDataset;
use Hotspot\HotspotDataset\Domain\DatasetRepository\DatasetRepository;
use Hotspot\HotspotDataset\Domain\Image\ImageId;
use Hotspot\Test\HotspotDataset\Domain\Image\TestImageBuilder;
use PHPUnit\Framework\TestCase;
use Shared\Domain\Uuid;

class DatasetStatsTestUtils extends TestCase
{
    /**
     * @param array<string, mixed>|null $stats
     * @return array<string, mixed>
     */
    public function createDatasetStats(array|null $stats = null): array
    {
        $stats = !is_null($stats) ? $stats : [];

        $stats['id'] = array_key_exists('id', $stats)
            ? $stats['id']
            : Uuid::random()->value();

        $stats['name'] = array_key_exists('name', $stats)
            ? $stats['name']
            : "Dataset {$stats['id']}";

        $stats['date'] = array_key_exists('date', $stats)
            ? $stats['date']
            : (new DateTime())->format('Y/m/d H:i:s');

        $stats['numImages'] = array_key_exists('numImages', $stats)
            ? $stats['numImages']
            : mt_rand(0, 5);

        $stats['imageIds'] = array_key_exists('imageIds', $stats)
            ? $stats['imageIds']
            : ($stats['numImages'] > 0
                ? array_map(fn($id) => ImageId::random()->value(), range(1, $stats['numImages']))
                : []);

        return $stats;
    }

    /** @param array<string, mixed> $stats */
    public function createDatasetWithStats(DatasetRepository $repository, array &$stats): void
    {
        $images = [];
        foreach ($stats['imageIds'] as $imageId) {
            $images[] = TestImageBuilder::random()
                ->withId(ImageId::fromString($imageId))
                ->withName("Image {$imageId}")
                ->withContent($imageId)
                ->build();
        }

        $dataset = new InMemoryDataset(
            Uuid::fromString($stats['id']),
            DatasetName::create($stats['name']),
            $images,
            new DateTime($stats['date'])
        );

        $stats['size'] = $dataset->getSize();

        $repository->save($dataset);
    }

    /**
     * @param array<int, array<string, mixed>> $expected
     * @param array<int, array<string, mixed>> $actual
     */
    public function assertDatasetStatsMatch(array $expected, array $actual): void
    {
        $expected = self::orderDatasetStats($expected);
        $actual = self::orderDatasetStats($actual);
        $this->assertEquals($actual, $expected);
    }

    /**
     * @param array<int, array<string, mixed>> $datasetStats
     * @return array<array<string, mixed>>
     */
    private function orderDatasetStats(array $datasetStats): array
    {
        $indexedStats = [];
        foreach ($datasetStats as $stats) {
            $imageIds = [];
            foreach ($stats['imageIds'] as $id) {
                $imageIds[$id] = true;
            }

            $stats['imageIds'] = $imageIds;
            $indexedStats[$stats['id']] = $stats;
        }

        return $indexedStats;
    }
}
