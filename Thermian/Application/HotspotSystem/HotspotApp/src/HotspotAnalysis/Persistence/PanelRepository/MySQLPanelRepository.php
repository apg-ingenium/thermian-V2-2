<?php
declare(strict_types=1);

namespace Hotspot\HotspotAnalysis\Persistence\PanelRepository;

use Cake\Database\Connection;
use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\AnalysisId;
use Hotspot\HotspotAnalysis\Domain\PanelRepository\PanelRepository;
use Hotspot\HotspotAnalysis\Persistence\PanelRepository\PanelEntity\PanelEntity;
use Hotspot\HotspotAnalysis\Persistence\PanelRepository\PanelEntity\PanelEntityBuilder;
use Hotspot\HotspotDataset\Domain\Image\ImageId;
use Shared\Domain\Uuid;
use Shared\Persistence\DuplicateIdException;

class MySQLPanelRepository implements PanelRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function save(PanelEntity $panel): void
    {
        if ($this->containsId($panel->getId())) {
            throw new DuplicateIdException("Duplicate panel id {$panel->getId()->value()}");
        }

        $sql = "
            insert into panels (
                id, analysis_id, image_id, panel_index,
                score, x_min, x_max, y_min, y_max
            )
            values (
                :id, :analysis_id, :image_id, :panel_index,
                :score, :x_min, :x_max, :y_min, :y_max
            )
        ";

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('id', $panel->getId()->binary());
        $statement->bindValue('analysis_id', $panel->getAnalysisId()->binary());
        $statement->bindValue('image_id', $panel->getImageId()->binary());
        $statement->bindValue('panel_index', $panel->getIndex());
        $statement->bindValue('score', $panel->getScore());
        $statement->bindValue('x_min', $panel->getXMin());
        $statement->bindValue('x_max', $panel->getXMax());
        $statement->bindValue('y_min', $panel->getYMin());
        $statement->bindValue('y_max', $panel->getYMax());
        $statement->execute();
    }

    /** @param array<\Hotspot\HotspotAnalysis\Persistence\PanelRepository\PanelEntity\PanelEntity> $panels */
    public function saveAll(array $panels): void
    {
        if (empty($panels)) {
            return;
        }

        $panelIds = array_map(fn(PanelEntity $panel) => $panel->getId(), $panels);

        if ($this->containsAnyId($panelIds)) {
            throw new DuplicateIdException('Duplicate panel id');
        }

        $statement = $this->connection->newQuery()
            ->insert(['id', 'analysis_id', 'image_id', 'panel_index', 'score', 'x_min', 'x_max', 'y_min', 'y_max'])
            ->into('panels');

        foreach ($panels as $panel) {
            $statement->values([
                'id' => $panel->getId()->binary(),
                'analysis_id' => $panel->getAnalysisId()->binary(),
                'image_id' => $panel->getImageId()->binary(),
                'panel_index' => $panel->getIndex(),
                'score' => $panel->getScore(),
                'x_min' => $panel->getXMin(),
                'x_max' => $panel->getXMax(),
                'y_min' => $panel->getYMin(),
                'y_max' => $panel->getYMax(),
            ]);
        }

        $statement->execute();
    }

    /** @param array<\Shared\Domain\Uuid> $panelIds */
    public function containsAnyId(array $panelIds): bool
    {
        if (empty($panelIds)) {
            return false;
        }

        $ids = array_map(fn($id) => $id->binary(), $panelIds);

        $exists = $this->connection
            ->newQuery()
            ->select(['id'])
            ->from('panels')
            ->where(['id in' => $ids])
            ->execute()
            ->fetch('assoc');

        return $exists !== false;
    }

    /**
     * @inheritDoc
     */
    public function findByAnalysisId(AnalysisId $analysisId): array
    {
        $sql = 'select id, analysis_id, image_id, panel_index, score, x_min, x_max, y_min, y_max
                from panels where analysis_id = :analysis_id';

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('analysis_id', $analysisId->binary());
        $statement->execute();

        $panels = [];

        while (($row = $statement->fetch('assoc'))) {
            $panels[] = PanelEntityBuilder::panel()
                ->withId(Uuid::fromBinary($row['id']))
                ->withAnalysisId(AnalysisId::fromBinary($row['analysis_id']))
                ->withImageId(ImageId::fromBinary($row['image_id']))
                ->withIndex(intval($row['panel_index']))
                ->withScore(floatval($row['score']))
                ->withYMin(intval($row['y_min']))
                ->withXMin(intval($row['x_min']))
                ->withYMax(intval($row['y_max']))
                ->withXMax(intval($row['x_max']))
                ->build();
        }

        return $panels;
    }

    public function findByRecordId(AnalysisId $analysisId, ImageId $imageId): array
    {
        $sql = 'select id, analysis_id, image_id, panel_index, score, x_min, x_max, y_min, y_max
                from panels where analysis_id = :analysis_id and image_id = :image_id';

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('analysis_id', $analysisId->binary());
        $statement->bindValue('image_id', $imageId->binary());
        $statement->execute();

        $panels = [];

        while (($row = $statement->fetch('assoc'))) {
            $panels[] = PanelEntityBuilder::panel()
                ->withId(Uuid::fromBinary($row['id']))
                ->withAnalysisId($analysisId)
                ->withImageId($imageId)
                ->withIndex(intval($row['panel_index']))
                ->withScore(floatval($row['score']))
                ->withYMin(intval($row['y_min']))
                ->withXMin(intval($row['x_min']))
                ->withYMax(intval($row['y_max']))
                ->withXMax(intval($row['x_max']))
                ->build();
        }

        return $panels;
    }

    /** @return array<\Shared\Domain\Uuid> */
    public function findPanelIdsByRecordId(AnalysisId $analysisId, ImageId $imageId): array
    {
        $sql = 'select id from panels where analysis_id = :analysis_id and image_id = :image_id';

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('analysis_id', $analysisId->binary());
        $statement->bindValue('image_id', $imageId->binary());
        $statement->execute();

        $panelIds = [];

        while (($row = $statement->fetch('assoc'))) {
            $panelIds[] = Uuid::fromBinary($row['id']);
        }

        return $panelIds;
    }

    public function containsId(Uuid $id): bool
    {
        $sql = 'select id from panels where id = :id';

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('id', $id->binary());
        $statement->execute();

        $exists = $statement->fetch('assoc');

        return $exists !== false;
    }

    public function removeByRecordId(AnalysisId $analysisId, ImageId $imageId): void
    {
        $sql = 'delete from panels where analysis_id = :analysis_id and image_id = :image_id';

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('analysis_id', $analysisId->binary());
        $statement->bindValue('image_id', $imageId->binary());
        $statement->execute();
    }

    public function removeAll(): void
    {
        $this->connection->execute('delete from panels');
    }
}
