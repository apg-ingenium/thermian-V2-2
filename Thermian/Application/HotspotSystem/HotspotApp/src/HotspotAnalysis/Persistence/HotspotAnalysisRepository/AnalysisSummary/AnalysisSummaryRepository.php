<?php
declare(strict_types=1);

namespace Hotspot\HotspotAnalysis\Persistence\HotspotAnalysisRepository\AnalysisSummary;

use Cake\Database\Connection;
use DateTime;
use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\AnalysisId;
use Hotspot\HotspotAnalysis\Domain\HotspotAnalysisRepository\DuplicateAnalysisIdException;

class AnalysisSummaryRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function save(AnalysisSummary $analysisDetails): void
    {
        if ($this->containsId($analysisDetails->getId())) {
            throw DuplicateAnalysisIdException::forId(
                $analysisDetails->getId()
            );
        }

        $sql = 'insert into analysis (id, date, target, num_records, num_panels, num_hotspots)
                values (:id, :date, :target, :num_records, :num_panels, :num_hotspots)';

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('id', $analysisDetails->getId()->binary());
        $statement->bindValue('date', $analysisDetails->getCreationDate(), 'datetime');
        $statement->bindValue('target', $analysisDetails->getTarget());
        $statement->bindValue('num_records', $analysisDetails->getNumRecords());
        $statement->bindValue('num_panels', $analysisDetails->getNumPanels());
        $statement->bindValue('num_hotspots', $analysisDetails->getNumHotspots());
        $statement->execute();
    }

    public function containsId(AnalysisId $analysisId): bool
    {
        $sql = 'select id from analysis where id = :id';

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('id', $analysisId->binary());
        $statement->execute();

        $exists = $statement->fetch('assoc');

        return $exists !== false;
    }

    public function findById(AnalysisId $analysisId): ?AnalysisSummary
    {
        $sql = 'select id, date, target, num_records, num_panels, num_hotspots from analysis where id = :id';

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('id', $analysisId->binary());
        $statement->execute();

        $row = $statement->fetch('assoc');

        if (!$row) {
            return null;
        }

        return AnalysisSummary::create(
            AnalysisId::fromBinary($row['id']),
            $row['target'],
            new DateTime($row['date']),
            intval($row['num_records']),
            intval($row['num_panels']),
            intval($row['num_hotspots']),
        );
    }

    public function removeById(AnalysisId $analysisId): void
    {
        $sql = 'delete from analysis where id = :id';

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('id', $analysisId->binary());
        $statement->execute();
    }

    public function removeAll(): void
    {
        $this->connection->execute('delete from analysis');
    }
}
