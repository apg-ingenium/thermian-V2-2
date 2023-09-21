<?php
declare(strict_types=1);

namespace Hotspot\HotspotResults\Persistence\OutputCsvRepository;

use Cake\Database\Connection;
use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\AnalysisId;
use Hotspot\HotspotDataset\Domain\Image\ImageId;
use Hotspot\HotspotResults\Domain\HotspotCsv\HotspotCsvBuilder;
use Hotspot\HotspotResults\Domain\OutputCsvRepository\OutputCsvRepository;

class MySQLOutputCsvRepository implements OutputCsvRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function findByRecordId(AnalysisId $analysisId, ImageId $imageId): array
    {
        $sql = 'select name, content from output_csv where analysis_id = :analysis_id and image_id = :image_id';

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('analysis_id', $analysisId->binary());
        $statement->bindValue('image_id', $imageId->binary());
        $statement->execute();

        $files = [];

        while (($row = $statement->fetch('assoc')) !== false) {
            $files[$row['name']] = HotspotCsvBuilder::hotspotCsv()
                ->withAnalysisId($analysisId)
                ->withImageId($imageId)
                ->withName($row['name'])
                ->withContent($row['content'])
                ->build();
        }

        return $files;
    }

    /** @inheritDoc */
    public function findByAnalysisId(AnalysisId $analysisId): array
    {
        $sql = 'select image_id, name, content from output_csv where analysis_id = :analysis_id';

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('analysis_id', $analysisId->binary());
        $statement->execute();

        $files = [];

        while (($row = $statement->fetch('assoc')) !== false) {
            $imageId = ImageId::fromBinary($row['image_id']);

            if (!array_key_exists($imageId->value(), $files)) {
                $files[$imageId->value()] = [];
            }

            $files[$imageId->value()][$row['name']] = HotspotCsvBuilder::hotspotCsv()
                ->withAnalysisId($analysisId)
                ->withImageId($imageId)
                ->withName($row['name'])
                ->withContent($row['content'])
                ->build();
        }

        return $files;
    }
}
