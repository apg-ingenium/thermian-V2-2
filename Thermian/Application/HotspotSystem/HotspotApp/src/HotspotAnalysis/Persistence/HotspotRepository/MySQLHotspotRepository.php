<?php
declare(strict_types=1);

namespace Hotspot\HotspotAnalysis\Persistence\HotspotRepository;

use Cake\Database\Connection;
use Hotspot\HotspotAnalysis\Domain\Hotspot\HotspotId;
use Hotspot\HotspotAnalysis\Domain\HotspotAnalysisRepository\DuplicateHotspotIdException;
use Hotspot\HotspotAnalysis\Domain\HotspotRepository\HotspotRepository;
use Hotspot\HotspotAnalysis\Persistence\HotspotRepository\HotspotEntity\HotspotEntity;
use Shared\Domain\Uuid;

class MySQLHotspotRepository implements HotspotRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function save(HotspotEntity $hotspot): void
    {
        if ($this->containsId($hotspot->getId())) {
            throw DuplicateHotspotIdException::forId(
                $hotspot->getId()
            );
        }

        $sql = "
            insert into hotspots (
                id, panel_id, hotspot_index, score,
                x_min, x_max, y_min, y_max
            )
            values (
                :id, :panel_id, :hotspot_index, :score,
                :x_min, :x_max, :y_min, :y_max
            )
        ";

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('id', $hotspot->getId()->binary());
        $statement->bindValue('panel_id', $hotspot->getPanelId()->binary());
        $statement->bindValue('hotspot_index', $hotspot->getIndex());
        $statement->bindValue('score', $hotspot->getScore());
        $statement->bindValue('x_min', $hotspot->getXMin());
        $statement->bindValue('x_max', $hotspot->getXMax());
        $statement->bindValue('y_min', $hotspot->getYMin());
        $statement->bindValue('y_max', $hotspot->getYMax());
        $statement->execute();
    }

    public function saveAll(array $hotspots): void
    {
        if (empty($hotspots)) {
            return;
        }

        $hotspotIds = array_map(fn(HotspotEntity $hotspot) => $hotspot->getId(), $hotspots);

        if ($this->containsAnyId($hotspotIds)) {
            throw DuplicateHotspotIdException::create();
        }

        $statement = $this->connection->newQuery()
            ->insert(['id', 'panel_id', 'hotspot_index', 'score', 'x_min', 'x_max', 'y_min', 'y_max'])
            ->into('hotspots');

        foreach ($hotspots as $hotspot) {
            $statement->values([
                'id' => $hotspot->getId()->binary(),
                'panel_id' => $hotspot->getPanelId()->binary(),
                'hotspot_index' => $hotspot->getIndex(),
                'score' => $hotspot->getScore(),
                'x_min' => $hotspot->getXMin(),
                'x_max' => $hotspot->getXMax(),
                'y_min' => $hotspot->getYMin(),
                'y_max' => $hotspot->getYMax(),
            ]);
        }

        $statement->execute();
    }

    public function containsId(HotspotId $hotspotId): bool
    {
        $sql = 'select id from hotspots where id = :id';

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('id', $hotspotId->binary());
        $statement->execute();

        $exists = $statement->fetch('assoc');

        return $exists !== false;
    }

    /**
     * @param \Hotspot\HotspotAnalysis\Domain\Hotspot\HotspotId[] $hotspotIds
     * @return bool
     */
    public function containsAnyId(array $hotspotIds): bool
    {
        if (empty($hotspotIds)) {
            return false;
        }

        $ids = array_map(fn($id) => $id->binary(), $hotspotIds);

        $exists = $this->connection
            ->newQuery()
            ->select(['id'])
            ->from('hotspots')
            ->whereInList('id', $ids)
            ->execute()
            ->fetch('assoc');

        return $exists !== false;
    }

    public function findById(HotspotId $hotspotId): ?HotspotEntity
    {
        $sql = "
            select 
                id, panel_id, hotspot_index, score,
                x_min, x_max, y_min, y_max
            from hotspots
            where id = :id
        ";

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('id', $hotspotId->binary());
        $statement->execute();

        $row = $statement->fetch('assoc');

        if (!$row) {
            return null;
        }

        return HotspotEntity::create(
            Uuid::fromBinary($row['panel_id']),
            intval($row['hotspot_index']),
            floatval($row['score']),
            intval($row['x_min']),
            intval($row['x_max']),
            intval($row['y_min']),
            intval($row['y_max']),
            HotspotId::fromBinary($row['id'])
        );
    }

    /**
     * @param array<\Shared\Domain\Uuid> $panelIds
     * @return array<\Hotspot\HotspotAnalysis\Persistence\HotspotRepository\HotspotEntity\HotspotEntity>
     */
    public function findByMultiplePanelIds(array $panelIds): array
    {
        if (empty($panelIds)) {
            return [];
        }

        $panelIds = array_map(fn(Uuid $id) => $id->binary(), $panelIds);

        $statement = $this->connection->newQuery()
            ->select(['id', 'panel_id', 'hotspot_index', 'score', 'x_min', 'x_max', 'y_min', 'y_max'])
            ->from(['hotspots'])
            ->where(['panel_id in' => $panelIds])
            ->execute();

        $hotspots = [];

        while (($row = $statement->fetch('assoc'))) {
            $hotspots[] =
                HotspotEntity::create(
                    Uuid::fromBinary($row['panel_id']),
                    intval($row['hotspot_index']),
                    floatval($row['score']),
                    intval($row['x_min']),
                    intval($row['x_max']),
                    intval($row['y_min']),
                    intval($row['y_max']),
                    HotspotId::fromBinary($row['id'])
                );
        }

        return $hotspots;
    }

    /** @return array<\Hotspot\HotspotAnalysis\Persistence\HotspotRepository\HotspotEntity\HotspotEntity> */
    public function findByPanelId(Uuid $panelId): array
    {
        $sql = "
            select 
                id, panel_id, hotspot_index, score,
                x_min, x_max, y_min, y_max
            from hotspots
            where panel_id = :panel_id
        ";

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('panel_id', $panelId->binary());
        $statement->execute();

        $hotspots = [];

        while (($row = $statement->fetch('assoc'))) {
            $hotspots[] =
                HotspotEntity::create(
                    Uuid::fromBinary($row['panel_id']),
                    intval($row['hotspot_index']),
                    floatval($row['score']),
                    intval($row['x_min']),
                    intval($row['x_max']),
                    intval($row['y_min']),
                    intval($row['y_max']),
                    HotspotId::fromBinary($row['id'])
                );
        }

        return $hotspots;
    }

    public function removeById(HotspotId $hotspotId): void
    {
        $sql = 'delete from hotspots where id = :id';

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('id', $hotspotId->binary());
        $statement->execute();
    }

    public function removeByPanelId(Uuid $panelId): void
    {
        $sql = 'delete from hotspots where panel_id = :panel_id';

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('panel_id', $panelId->binary());
        $statement->execute();
    }

    /** @param array<\Shared\Domain\Uuid> $panelIds */
    public function removeByMultiplePanelIds($panelIds): void
    {
        if (empty($panelIds)) {
            return;
        }

        $panelIds = array_map(fn(Uuid $id) => $id->binary(), $panelIds);

        $this->connection->newQuery()
            ->delete()
            ->from(['hotspots'])
            ->where(['panel_id in' => $panelIds])
            ->execute();
    }

    public function removeAll(): void
    {
        $this->connection->execute('delete from hotspots');
    }
}
