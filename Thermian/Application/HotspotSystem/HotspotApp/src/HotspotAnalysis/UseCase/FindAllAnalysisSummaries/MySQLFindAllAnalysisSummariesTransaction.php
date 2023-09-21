<?php
declare(strict_types=1);

namespace Hotspot\HotspotAnalysis\UseCase\FindAllAnalysisSummaries;

use Cake\Database\Connection;
use DateTime;
use Shared\Domain\Uuid;

class MySQLFindAllAnalysisSummariesTransaction implements FindAllAnalysisSummariesTransaction
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /** @inheritDoc */
    public function execute(): array
    {
        $sql = 'select id, target, date, num_records, num_panels, num_hotspots,
                (select image_id from image_analysis where analysis_id = id limit 1) as record_id
                from analysis
                ';

        $statement = $this->connection->execute($sql);

        $summaries = [];
        while (($row = $statement->fetch('assoc'))) {
            $summaries[] = [
                'analysisId' => Uuid::fromBinary($row['id'])->value(),
                'imageId' => Uuid::fromBinary($row['record_id'])->value(),
                'target' => $row['target'],
                'date' => (new DateTime($row['date']))->format('Y/m/d H:i:s'),
                'numImages' => intval($row['num_records']),
                'numPanels' => intval($row['num_panels']),
                'numHotspots' => intval($row['num_hotspots']),
            ];
        }

        return $summaries;
    }
}
