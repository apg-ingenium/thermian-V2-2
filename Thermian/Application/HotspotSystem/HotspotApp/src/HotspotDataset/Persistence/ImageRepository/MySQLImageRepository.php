<?php
declare(strict_types=1);

namespace Hotspot\HotspotDataset\Persistence\ImageRepository;

use Cake\Database\Connection;
use Hotspot\HotspotDataset\Domain\Image\Image;
use Hotspot\HotspotDataset\Domain\Image\ImageId;
use Hotspot\HotspotDataset\Domain\ImageRepository\DuplicateImageIdException;
use Hotspot\HotspotDataset\Domain\ImageRepository\ImageRepository;
use Shared\Persistence\DuplicateIdException;

class MySQLImageRepository implements ImageRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function save(Image $image): void
    {
        if ($this->containsId($image->getId())) {
            throw DuplicateImageIdException::forId($image->getId());
        }

        $sql = 'insert into image (id, name, format, size, content) values (:id, :name, :format, :size, :content)';

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('id', $image->getId()->binary());
        $statement->bindValue('name', $image->getName());
        $statement->bindValue('format', $image->getFormat());
        $statement->bindValue('size', $image->getSize());
        $statement->bindValue('content', $image->getContent());
        $statement->execute();
    }

    public function containsId(ImageId $imageId): bool
    {
        $sql = 'select id from image where id = :id';

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('id', $imageId->binary());
        $statement->execute();

        $exists = $statement->fetch('assoc');

        return $exists !== false;
    }

    /** @param iterable<\Hotspot\HotspotDataset\Domain\Image\Image> $images */
    public function saveAll(iterable $images): void
    {
        /** @var array<\Hotspot\HotspotDataset\Domain\Image\Image> $images */
        $images = [...$images];

        if (empty($images)) {
            return;
        }

        $imageIds = array_map(fn(Image $image) => $image->getId(), $images);

        if ($this->containsAnyId($imageIds)) {
            throw new DuplicateIdException('Duplicate Image Id');
        }

        $statement = $this->connection->newQuery()
            ->insert(['id', 'name', 'format', 'size', 'content'])
            ->into('image');

        foreach ($images as $image) {
            $statement->values([
                'id' => $image->getId()->binary(),
                'name' => $image->getName(),
                'format' => $image->getFormat(),
                'size' => $image->getSize(),
                'content' => $image->getContent(),
            ]);
        }

        $statement->execute();
    }

    /** @param array<\Hotspot\HotspotDataset\Domain\Image\ImageId> $imageIds */
    public function containsAnyId(array $imageIds): bool
    {
        if (empty($imageIds)) {
            return false;
        }

        $ids = array_map(fn($id) => $id->binary(), $imageIds);

        $exists = $this->connection
            ->newQuery()
            ->select(['id'])
            ->from('image')
            ->whereInList('id', $ids)
            ->execute()
            ->fetch('assoc');

        return $exists !== false;
    }

    /** @inheritDoc */
    public function containsAllIds(array $imageIds): bool
    {
        if (empty($imageIds)) {
            return true;
        }

        $ids = array_map(fn($id) => $id->binary(), $imageIds);

        $result = $this->connection
            ->newQuery()
            ->select(['count(id) as num_ids'])
            ->from('image')
            ->whereInList('id', $ids)
            ->execute()
            ->fetch('assoc');

        return intval($result['num_ids']) === count($imageIds);
    }

    public function findById(ImageId $imageId): ?Image
    {
        $sql = 'select id, name, content from image where id = :id';

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('id', $imageId->binary());
        $statement->execute();

        $row = $statement->fetch('assoc');

        if (!$row) {
            return null;
        }

        return Image::create(
            ImageId::fromBinary($row['id']),
            $row['content'],
            $row['name']
        );
    }

    /**
     * @param array<\Hotspot\HotspotDataset\Domain\Image\ImageId> $imageIds
     * @return array<string>
     */
    public function findImageNames(array $imageIds): array
    {
        if (empty($imageIds)) {
            return [];
        }

        $imageIds = array_map(fn($imageId) => $imageId->binary(), $imageIds);

        $statement = $this->connection
            ->newQuery()
            ->select(['id', 'name'])
            ->from('image')
            ->whereInList('id', $imageIds)
            ->execute();

        $imageNames = [];
        while (($row = $statement->fetch('assoc'))) {
            $imageId = ImageId::fromBinary($row['id'])->value();
            $imageNames[$imageId] = $row['name'];
        }

        return $imageNames;
    }

    public function removeById(ImageId $imageId): void
    {
        $sql = 'delete from image where id = :id';

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('id', $imageId->binary());
        $statement->execute();
    }

    /** @inheritDoc */
    public function removeAllById(array $imageIds): void
    {
        if (empty($imageIds)) {
            return;
        }

        $imageIds = array_map(fn($imageId) => $imageId->binary(), $imageIds);

        $this->connection
            ->newQuery()
            ->delete()
            ->from('image')
            ->whereInList('id', $imageIds)
            ->execute();
    }

    public function removeAll(): void
    {
        $this->connection->execute('delete from image');
    }

    /** @inheritDoc */
    public function findAllIdsExcept(array $excludedIds): array
    {
        if (empty($excludedIds)) {
            return $this->findAllIds();
        }

        $excludedIds = array_map(fn($id) => $id->binary(), $excludedIds);

        $statement = $this->connection
            ->newQuery()
            ->select(['id'])
            ->from('image')
            ->whereNotInList('id', $excludedIds)
            ->execute();

        $ids = [];
        while (($row = $statement->fetch('assoc'))) {
            $ids[] = ImageId::fromBinary($row['id']);
        }

        return $ids;
    }

    /** @return array<\Hotspot\HotspotDataset\Domain\Image\ImageId> */
    private function findAllIds(): array
    {
        $sql = 'select id from image';
        $statement = $this->connection->execute($sql);

        $ids = [];
        while (($row = $statement->fetch('assoc'))) {
            $ids[] = ImageId::fromBinary($row['id']);
        }

        return $ids;
    }
}
