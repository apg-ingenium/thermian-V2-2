<?php
declare(strict_types=1);

namespace Hotspot\HotspotDataset\Persistence\DatasetRepository;

use Cake\Database\Connection;
use DateTime;
use Hotspot\HotspotDataset\Domain\Dataset\Dataset;
use Hotspot\HotspotDataset\Domain\Dataset\DatasetName;
use Hotspot\HotspotDataset\Domain\Dataset\DatasetStats;
use Hotspot\HotspotDataset\Domain\Dataset\DatasetStatsBuilder;
use Hotspot\HotspotDataset\Domain\Dataset\MaxDatasetSizeExceededException;
use Hotspot\HotspotDataset\Domain\Dataset\MaxNumDatasetImagesExceededException;
use Hotspot\HotspotDataset\Domain\DatasetRepository\DatasetNotFoundException;
use Hotspot\HotspotDataset\Domain\DatasetRepository\DatasetRepository;
use Hotspot\HotspotDataset\Domain\Image\ImageId;
use Hotspot\HotspotDataset\Domain\ImageRepository\ImageRepository;
use Shared\Domain\Uuid;
use Shared\Persistence\DuplicateIdException;

class MySQLDatasetRepository implements DatasetRepository
{
    public const MAX_NUM_DATASET_IMAGES = 100;
    public const MAX_DATASET_SIZE = 150 * 1024 * 1024;

    private Connection $connection;
    private ImageRepository $imageRepository;
    private int $batchSize;

    public function __construct(Connection $connection, ImageRepository $imageRepository)
    {
        $this->connection = $connection;
        $this->imageRepository = $imageRepository;
        $this->batchSize = 20;
    }

    public function save(Dataset $dataset): void
    {
        $datasetId = $dataset->getId();
        $imageIds = $dataset->getImageIds();

        if ($this->containsId($datasetId)) {
            throw new DuplicateIdException("Duplicate dataset id {$datasetId->value()}");
        }

        if ($this->datasetContainsAnyImage($datasetId, $imageIds)) {
            throw new DuplicateIdException('The dataset already contains an image with the given id');
        }

        foreach ($dataset->batchesOfSize($this->batchSize) as $batch) {
            $this->imageRepository->saveAll($batch);
        }

        $sql = 'insert into datasets (id, name, date, num_images, size) values (:id, :name, :date, :num_images, :size)';

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('id', $dataset->getId()->binary());
        $statement->bindValue('name', $dataset->getName()->value());
        $statement->bindValue('date', $dataset->getCreationDate(), 'datetime');
        $statement->bindValue('num_images', $dataset->getNumImages());
        $statement->bindValue('size', $dataset->getSize());
        $statement->execute();

        if ($dataset->getNumImages() === 0) {
            return;
        }

        $statement = $this->connection->newQuery()
            ->insert(['dataset_id', 'image_id'])
            ->into('dataset_image');

        foreach ($imageIds as $imageId) {
            $statement->values([
                'dataset_id' => $datasetId->binary(),
                'image_id' => $imageId->binary(),
            ]);
        }

        $statement->execute();
    }

    public function containsId(Uuid $datasetId): bool
    {
        $sql = 'select id from datasets where id = :id';

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('id', $datasetId->binary());
        $statement->execute();

        $exists = $statement->fetch('assoc');

        return $exists !== false;
    }

    /** @param array<\Hotspot\HotspotDataset\Domain\Image\ImageId> $imageIds */
    private function datasetContainsAnyImage(Uuid $datasetId, array $imageIds): bool
    {
        if (empty($imageIds)) {
            return false;
        }

        $ids = array_map(fn($id) => $id->binary(), $imageIds);

        $exists = $this->connection
            ->newQuery()
            ->select(['image_id'])
            ->from('dataset_image')
            ->whereInList('image_id', $ids)
            ->andWhere(['dataset_id' => $datasetId->binary()])
            ->execute()
            ->fetch('assoc');

        return $exists !== false;
    }

    public function addDatasetImages(Dataset $newImages): void
    {
        if ($newImages->getNumImages() === 0) {
            return;
        }

        $datasetId = $newImages->getId();

        $sql = 'select num_images, size from datasets where id = :dataset_id';

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('dataset_id', $datasetId->binary());
        $statement->execute();

        $row = $statement->fetch('assoc');

        if (!$row) {
            throw DatasetNotFoundException::withId($datasetId->value());
        }

        $currentNumImages = intval($row['num_images']);
        $numNewImages = $newImages->getNumImages();
        $numImages = $currentNumImages + $numNewImages;

        if ($numImages > self::MAX_NUM_DATASET_IMAGES) {
            throw MaxNumDatasetImagesExceededException::create();
        }

        $currentSize = intval($row['size']);
        $sizeNewImages = $newImages->getSize();
        $totalSize = $currentSize + $sizeNewImages;

        if ($totalSize > self::MAX_DATASET_SIZE) {
            throw MaxDatasetSizeExceededException::create();
        }

        if ($this->datasetContainsAnyImage($datasetId, $newImages->getImageIds())) {
            throw new DuplicateIdException('The dataset already contains an image with the given id');
        }

        $this->connection->transactional(function (Connection $connection) use ($newImages, $numImages, $totalSize) {

            $datasetId = $newImages->getId()->binary();

            foreach ($newImages->batchesOfSize($this->batchSize) as $batch) {
                $this->imageRepository->saveAll($batch);
            }

            $statement = $connection->newQuery()
                ->insert(['dataset_id', 'image_id'])
                ->into('dataset_image');

            foreach ($newImages->getImageIds() as $imageId) {
                $statement->values([
                    'dataset_id' => $datasetId,
                    'image_id' => $imageId->binary(),
                ]);
            }

            $statement->execute();

            $sql = 'update datasets set num_images = :num_images, size = :size where id = :dataset_id';

            $statement = $connection->prepare($sql);
            $statement->bindValue('num_images', $numImages);
            $statement->bindValue('size', $totalSize);
            $statement->bindValue('dataset_id', $datasetId);
            $statement->execute();
        });
    }

    public function findDatasetStatsById(Uuid $datasetId): ?DatasetStats
    {
        $sql = 'select id, name, date, num_images, size from datasets where id = :dataset_id';
        $statement = $this->connection->prepare($sql);
        $statement->bindValue('dataset_id', $datasetId->binary());
        $statement->execute();

        $dataset = $statement->fetch('assoc');
        if (!$dataset) {
            return null;
        }

        $imageIds = $this->findDatasetImageIds($datasetId);

        $stats = DatasetStatsBuilder
            ::datasetStats()
            ->withId(Uuid::fromBinary($dataset['id']))
            ->withName(DatasetName::create($dataset['name']))
            ->createdAt(new DateTime($dataset['date']))
            ->withSize(intval($dataset['size']))
            ->withImageIds($imageIds);

        return $stats->build();
    }

    /** @return array<\Hotspot\HotspotDataset\Domain\Image\ImageId> */
    private function findDatasetImageIds(Uuid $datasetId): array
    {
        $sql = 'select image_id from dataset_image where dataset_id = :dataset_id';
        $statement = $this->connection->prepare($sql);
        $statement->bindValue('dataset_id', $datasetId->binary());
        $statement->execute();

        $imageIds = [];
        while (($row = $statement->fetch('assoc'))) {
            $imageIds[] = ImageId::fromBinary($row['image_id']);
        }

        return $imageIds;
    }

    public function findAllDatasetStats(): array
    {
        $sql = 'select id, name, date, num_images, size from datasets';
        $statement = $this->connection->execute($sql);

        $datasets = [];
        while (($row = $statement->fetch('assoc'))) {
            $row['image_ids'] = [];
            $datasets[$row['id']] = $row;
        }

        $sql = 'select dataset_id, image_id from dataset_image';
        $statement = $this->connection->execute($sql);

        while (($row = $statement->fetch('assoc'))) {
            $datasets[$row['dataset_id']]['image_ids'][] = $row['image_id'];
        }

        $allDatasetStats = [];
        foreach ($datasets as $dataset) {
            $stats = DatasetStatsBuilder
                ::datasetStats()
                ->withId(Uuid::fromBinary($dataset['id']))
                ->withName(DatasetName::create($dataset['name']))
                ->withSize(intval($dataset['size']))
                ->createdAt(new DateTime($dataset['date']));

            foreach ($dataset['image_ids'] as $imageId) {
                $stats->withImageId(ImageId::fromBinary($imageId));
            }

            $allDatasetStats[] = $stats->build();
        }

        return $allDatasetStats;
    }

    /** @return array<\Hotspot\HotspotDataset\Domain\Image\ImageId> */
    private function findAllDatasetImageIds(): array
    {
        $sql = 'select distinct(image_id) from dataset_image';
        $statement = $this->connection->execute($sql);

        $imageIds = [];
        while (($row = $statement->fetch('assoc'))) {
            $imageIds[] = ImageId::fromBinary($row['image_id']);
        }

        return $imageIds;
    }

    /** @inheritDoc */
    public function findIndependentImageIds(): array
    {
        return $this->imageRepository->findAllIdsExcept(
            $this->findAllDatasetImageIds()
        );
    }

    public function removeIndependentImages(): void
    {
        $this->imageRepository->removeAllById(
            $this->findIndependentImageIds()
        );
    }

    public function removeById(Uuid $datasetId): void
    {
        $this->connection->transactional(function (Connection $connection) use ($datasetId) {
            $imageIds = $this->findDatasetImageIds($datasetId);
            $this->imageRepository->removeAllById($imageIds);

            $sql = 'delete from datasets where id = :dataset_id';

            $statement = $connection->prepare($sql);
            $statement->bindValue('dataset_id', $datasetId->binary());
            $statement->execute();
        });
    }

    public function removeImageById(ImageId $imageId): void
    {
        $sql = 'select dataset_id from dataset_image where image_id = :image_id';

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('image_id', $imageId->binary());
        $statement->execute();

        $row = $statement->fetch('assoc');

        if ($row) {
            $datasetId = $row['dataset_id'];

            $sql = 'select num_images from datasets where id = :dataset_id';

            $statement = $this->connection->prepare($sql);
            $statement->bindValue('dataset_id', $datasetId);
            $statement->execute();

            $row = $statement->fetch('assoc');
            assert($row !== false);
            $numImages = intval($row['num_images']);

            if ($numImages === 1) {
                $sql = 'delete from datasets where id = :dataset_id';

                $statement = $this->connection->prepare($sql);
                $statement->bindValue('dataset_id', $datasetId);
                $statement->execute();
            }
            else {
                $sql = 'select size from image where id = :image_id';

                $statement = $this->connection->prepare($sql);
                $statement->bindValue('image_id', $imageId->binary());
                $statement->execute();

                $row = $statement->fetch('assoc');
                assert($row !== false);
                $imageSize = intval($row['size']);

                $sql = 'update datasets set num_images = num_images - 1, size = size - :image_size where id = :dataset_id';

                $statement = $this->connection->prepare($sql);
                $statement->bindValue('dataset_id', $datasetId);
                $statement->bindValue('image_size', $imageSize);
                $statement->execute();
            }
        }

        $this->imageRepository->removeById($imageId);
    }

    public function removeAll(): void
    {
        $this->connection->transactional(function (Connection $connection) {
            $imageIds = $this->findAllDatasetImageIds();
            $this->imageRepository->removeAllById($imageIds);
            $connection->execute('delete from datasets');
        });
    }
}
