<?php
declare(strict_types=1);

namespace Hotspot\HotspotDataset\UseCase\ZipDataset;

use Cake\Database\Connection;
use Generator;
use Hotspot\HotspotDataset\Domain\Dataset\DatasetId;
use Hotspot\HotspotDataset\Domain\File\File;

class MySQLDatasetFinder implements DatasetFinder
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /** @inheritDoc */
    public function findDatasetById(DatasetId $datasetId): Generator
    {
        $datasetImageIds = $this->connection->newQuery()
            ->select('image_id')
            ->from('dataset_image')
            ->where('dataset_id = :dataset_id')
            ->bind(':dataset_id', $datasetId->binary());

        $statement = $this->connection->newQuery()
            ->disableBufferedResults()
            ->select('name, content')
            ->from('image')
            ->where(['id in' => $datasetImageIds])
            ->execute();

        while ($row = $statement->fetch('assoc')) {
            yield File::create($row['name'], $row['content']);
        }
    }
}
