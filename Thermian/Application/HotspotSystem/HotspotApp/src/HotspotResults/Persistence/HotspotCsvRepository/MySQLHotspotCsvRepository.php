<?php
declare(strict_types=1);

namespace Hotspot\HotspotResults\Persistence\HotspotCsvRepository;

use Cake\Database\Connection;
use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\AnalysisId;
use Hotspot\HotspotDataset\Domain\Image\ImageId;
use Hotspot\HotspotResults\Domain\HotspotCsv\HotspotCsv;
use Hotspot\HotspotResults\Domain\HotspotCsv\HotspotCsvBuilder;
use Hotspot\HotspotResults\Domain\HotspotCsvRepository\HotspotCsvRepository;
use Shared\Domain\Uuid;
use Shared\Persistence\DuplicateIdException;

class MySQLHotspotCsvRepository implements HotspotCsvRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function save(HotspotCsv $hotspotCsv): void
    {
        if ($this->containsId($hotspotCsv->getId())) {
            throw new DuplicateIdException(
                "Duplicate csv id {$hotspotCsv->getId()->value()}"
            );
        }

        $sql = "
            insert into output_csv (id, analysis_id, image_id, name, size, content)
            values (:id, :analysis_id, :image_id, :name, :size, :content)
        ";

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('id', $hotspotCsv->getId()->binary());
        $statement->bindValue('analysis_id', $hotspotCsv->getAnalysisId()->binary());
        $statement->bindValue('image_id', $hotspotCsv->getImageId()->binary());
        $statement->bindValue('name', $hotspotCsv->getName());
        $statement->bindValue('size', $hotspotCsv->getSize());
        $statement->bindValue('content', $hotspotCsv->getContent());
        $statement->execute();
    }

    public function containsId(Uuid $id): bool
    {
        $sql = 'select id from output_csv where id = :id';

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('id', $id->binary());
        $statement->execute();

        $exists = $statement->fetch('assoc');

        return $exists !== false;
    }

    public function findById(Uuid $id): ?HotspotCsv
    {
        $sql = 'select id, analysis_id, image_id, name, content from output_csv where id = :id';

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('id', $id->binary());
        $statement->execute();

        $row = $statement->fetch('assoc');

        return $row ? $this->mapToEntity($row) : null;
    }

    public function removeByAnalysisId(AnalysisId $analysisId): void
    {
        $sql = ' delete from output_csv where analysis_id = :analysis_id';

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('analysis_id', $analysisId->binary());
        $statement->execute();
    }

    public function findByRecordIdAndName(AnalysisId $analysisId, ImageId $imageId, string $name): ?HotspotCsv
    {
        $sql = "
            select id, analysis_id, image_id, name, content
            from output_csv
            where analysis_id = :analysis_id
              and image_id = :image_id
              and name like concat(:name, '%')
        ";

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('analysis_id', $analysisId->binary());
        $statement->bindValue('image_id', $imageId->binary());
        $statement->bindValue('name', $name);
        $statement->execute();

        $row = $statement->fetch('assoc');

        return $row ? $this->mapToEntity($row) : null;
    }

    public function containsRecordId(AnalysisId $analysisId, ImageId $imageId): bool
    {
        $sql = "
            select analysis_id, image_id
            from output_csv
            where analysis_id = :analysis_id 
              and image_id = :image_id
        ";

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('analysis_id', $analysisId->binary());
        $statement->bindValue('image_id', $imageId->binary());
        $statement->execute();

        $exists = $statement->fetch('assoc');

        return $exists !== false;
    }

    /** @return array<\Hotspot\HotspotResults\Domain\HotspotCsv\HotspotCsv> */
    public function findByRecordId(AnalysisId $analysisId, ImageId $imageId): array
    {
        $sql = "
            select id, analysis_id, image_id, name, content
            from output_csv
            where analysis_id = :analysis_id 
              and image_id = :image_id
        ";

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('analysis_id', $analysisId->binary());
        $statement->bindValue('image_id', $imageId->binary());
        $statement->execute();

        $recordCsvs = [];
        while (($row = $statement->fetch('assoc'))) {
            $id = Uuid::fromBinary($row['id'])->value();
            $recordCsvs[$id] = $this->mapToEntity($row);
        }

        return $recordCsvs;
    }

    public function removeByRecordId(AnalysisId $analysisId, ImageId $imageId): void
    {
        $sql = "
            delete from output_csv
            where analysis_id = :analysis_id
              and image_id = :image_id
        ";

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('analysis_id', $analysisId->binary());
        $statement->bindValue('image_id', $imageId->binary());
        $statement->execute();
    }

    public function removeAll(): void
    {
        $this->connection->execute('delete from output_csv');
    }

    /** @param array<mixed> $row */
    public function mapToEntity(array $row): HotspotCsv
    {
        return HotspotCsvBuilder::hotspotCsv()
            ->withId(Uuid::fromBinary($row['id']))
            ->withAnalysisId(AnalysisId::fromBinary($row['analysis_id']))
            ->withImageId(ImageId::fromBinary($row['image_id']))
            ->withContent($row['content'])
            ->withName($row['name'])
            ->build();
    }
}
