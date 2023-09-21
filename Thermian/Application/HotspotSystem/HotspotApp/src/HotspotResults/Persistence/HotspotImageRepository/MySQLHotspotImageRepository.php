<?php
declare(strict_types=1);

namespace Hotspot\HotspotResults\Persistence\HotspotImageRepository;

use Cake\Database\Connection;
use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\AnalysisId;
use Hotspot\HotspotDataset\Domain\Image\ImageId;
use Hotspot\HotspotResults\Domain\HotspotImage\HotspotImage;
use Hotspot\HotspotResults\Domain\HotspotImage\HotspotImageBuilder;
use Hotspot\HotspotResults\Domain\HotspotImageRepository\HotspotImageRepository;
use Shared\Domain\Uuid;
use Shared\Persistence\DuplicateIdException;

class MySQLHotspotImageRepository implements HotspotImageRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function save(HotspotImage $image): void
    {
        if ($this->containsId($image->getId())) {
            throw new DuplicateIdException("Duplicate hotspot image id {$image->getId()->value()}");
        }

        $sql = "
            insert into output_image (id, analysis_id, image_id, name, format, size, content)
            values (:id, :analysis_id, :image_id, :name, :format, :size, :content)
        ";

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('id', $image->getId()->binary());
        $statement->bindValue('analysis_id', $image->getAnalysisId()->binary());
        $statement->bindValue('image_id', $image->getImageId()->binary());
        $statement->bindValue('name', $image->getName());
        $statement->bindValue('format', $image->getFormat());
        $statement->bindValue('size', $image->getSize());
        $statement->bindValue('content', $image->getContent());
        $statement->execute();
    }

    public function containsId(Uuid $id): bool
    {
        $sql = 'select id from output_image where id = :id';

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('id', $id->binary());
        $statement->execute();

        $exists = $statement->fetch('assoc');

        return $exists !== false;
    }

    public function removeByAnalysisId(AnalysisId $analysisId): void
    {
        $sql = 'delete from output_image where analysis_id = :analysis_id';

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('analysis_id', $analysisId->binary());
        $statement->execute();
    }

    public function findByAnalysisIdImageIdAndName(AnalysisId $analysisId, ImageId $imageId, string $name): ?HotspotImage
    {
        $sql = "
            select name, content
            from output_image
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

        return $row ? HotspotImageBuilder::hotspotImage()
            ->withAnalysisId($analysisId)
            ->withImageId($imageId)
            ->withName($row['name'])
            ->withContent($row['content'])
            ->build()
            : null;
    }

    public function containsCompositeId(AnalysisId $analysisId, ImageId $imageId): bool
    {
        $sql = 'select id from output_image where analysis_id = :analysis_id and image_id = :image_id';

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('analysis_id', $analysisId->binary());
        $statement->bindValue('image_id', $imageId->binary());
        $statement->execute();

        $exists = $statement->fetch('assoc');

        return $exists !== false;
    }

    /**
     * @return array<\Hotspot\HotspotResults\Domain\HotspotImage\HotspotImage>
     */
    public function findByCompositeId(AnalysisId $analysisId, ImageId $imageId): array
    {
        $sql = 'select name, content from output_image where analysis_id = :analysis_id and image_id = :image_id';

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('analysis_id', $analysisId->binary());
        $statement->bindValue('image_id', $imageId->binary());
        $statement->execute();

        $images = [];

        while (($row = $statement->fetch('assoc'))) {
            $images[] = HotspotImageBuilder::hotspotImage()
            ->withAnalysisId($analysisId)
            ->withImageId($imageId)
            ->withName($row['name'])
            ->withContent($row['content'])
            ->build();
        }

        return $images;
    }

    public function removeByRecordId(AnalysisId $analysisId, ImageId $imageId): void
    {
        $sql = 'delete from output_image where analysis_id = :analysis_id and image_id = :image_id';

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('analysis_id', $analysisId->binary());
        $statement->bindValue('image_id', $imageId->binary());
        $statement->execute();
    }

    public function removeAll(): void
    {
        $this->connection->execute('delete from output_image');
    }
}
