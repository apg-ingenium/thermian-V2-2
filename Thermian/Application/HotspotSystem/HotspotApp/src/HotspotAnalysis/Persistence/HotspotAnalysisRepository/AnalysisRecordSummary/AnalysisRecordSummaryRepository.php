<?php
declare(strict_types=1);

namespace Hotspot\HotspotAnalysis\Persistence\HotspotAnalysisRepository\AnalysisRecordSummary;

use Cake\Database\Connection;
use Hotspot\HotspotAnalysis\Domain\GpsCoordinates\GpsCoordinates;
use Hotspot\HotspotAnalysis\Domain\GpsCoordinates\Latitude;
use Hotspot\HotspotAnalysis\Domain\GpsCoordinates\Longitude;
use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\AnalysisId;
use Hotspot\HotspotAnalysis\Domain\HotspotAnalysisRepository\DuplicateAnalysisIdException;
use Hotspot\HotspotDataset\Domain\Image\ImageId;

class AnalysisRecordSummaryRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @throws \Hotspot\HotspotAnalysis\Domain\HotspotAnalysisRepository\DuplicateAnalysisIdException
     */
    public function save(AnalysisRecordSummary $details): void
    {
        if ($this->containsAnalysisRecordId($details->getAnalysisId(), $details->getImageId())) {
            throw DuplicateAnalysisIdException::forId(
                $details->getAnalysisId()
            );
        }

        $sql = "
            insert into image_analysis (analysis_id, image_id, image_name, latitude, longitude, num_panels, num_hotspots)
            values (:analysis_id, :image_id, :image_name, :latitude, :longitude, :num_panels, :num_hotspots)
        ";

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('analysis_id', $details->getAnalysisId()->binary());
        $statement->bindValue('image_id', $details->getImageId()->binary());
        $statement->bindValue('image_name', $details->getImageName());
        $statement->bindValue('latitude', $details->getCoordinates()?->getLatitude()->decimalDegrees());
        $statement->bindValue('longitude', $details->getCoordinates()?->getLongitude()->decimalDegrees());
        $statement->bindValue('num_panels', $details->getNumPanels());
        $statement->bindValue('num_hotspots', $details->getNumHotspots());
        $statement->execute();
    }

    public function containsAnalysisRecordId(AnalysisId $analysisId, ImageId $imageId): bool
    {
        $sql = "
            select analysis_id
            from image_analysis
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

    /** @param array<\Hotspot\HotspotAnalysis\Persistence\HotspotAnalysisRepository\AnalysisRecordSummary\AnalysisRecordSummary> $recordStats */
    public function saveAll(array $recordStats): void
    {
        if (empty($recordStats)) {
            return;
        }

        $statement = $this->connection->newQuery()
            ->insert(['analysis_id', 'image_id', 'image_name', 'latitude', 'longitude', 'num_panels', 'num_hotspots'])
            ->into('image_analysis');

        foreach ($recordStats as $stats) {
            $statement->values([
                'analysis_id' => $stats->getAnalysisId()->binary(),
                'image_id' => $stats->getImageId()->binary(),
                'image_name' => $stats->getImageName(),
                'latitude' => $stats->getCoordinates()?->getLatitude()->decimalDegrees(),
                'longitude' => $stats->getCoordinates()?->getLongitude()->decimalDegrees(),
                'num_panels' => $stats->getNumPanels(),
                'num_hotspots' => $stats->getNumHotspots(),
            ]);
        }

        $statement->execute();
    }

    public function findByRecordId(AnalysisId $analysisId, ImageId $imageId): ?AnalysisRecordSummary
    {
        $sql = "
            select analysis_id, image_id, image_name, latitude, longitude, num_panels, num_hotspots
            from image_analysis
            where analysis_id = :analysis_id
              and image_id = :image_id
        ";

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('analysis_id', $analysisId->binary());
        $statement->bindValue('image_id', $imageId->binary());
        $statement->execute();

        $row = $statement->fetch('assoc');

        if (!$row) {
            return null;
        }

        return AnalysisRecordSummary::create(
            AnalysisId::fromBinary($row['analysis_id']),
            ImageId::fromBinary($row['image_id']),
            $row['image_name'],
            intval($row['num_panels']),
            intval($row['num_hotspots']),
            $row['latitude'] ? GpsCoordinates::fromLatitudeAndLongitude(
                Latitude::fromDecimalDegrees(floatval($row['latitude'])),
                Longitude::fromDecimalDegrees(floatval($row['longitude']))
            ) : null
        );
    }

    /** @return array<\Hotspot\HotspotAnalysis\Persistence\HotspotAnalysisRepository\AnalysisRecordSummary\AnalysisRecordSummary> */
    public function findByAnalysisId(AnalysisId $analysisId): array
    {
        $sql = "
            select analysis_id, image_id, image_name, latitude, longitude, num_panels, num_hotspots
            from image_analysis
            where analysis_id = :analysis_id
        ";

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('analysis_id', $analysisId->binary());
        $statement->execute();

        $records = [];

        while (($row = $statement->fetch('assoc'))) {
            $records[] = AnalysisRecordSummaryBuilder::analysisRecordSummary()
                ->withAnalysisId(AnalysisId::fromBinary($row['analysis_id']))
                ->withImageId(ImageId::fromBinary($row['image_id']))
                ->withImageName($row['image_name'])
                ->withNumPanels(intval($row['num_panels']))
                ->withNumHotspots(intval($row['num_hotspots']))
                ->withGpsCoordinates(
                    $row['latitude'] ? GpsCoordinates::fromLatitudeAndLongitude(
                        Latitude::fromDecimalDegrees(floatval($row['latitude'])),
                        Longitude::fromDecimalDegrees(floatval($row['longitude']))
                    ) : null
                )
                ->build();
        }

        return $records;
    }

    public function removeByAnalysisRecordId(AnalysisId $analysisId, ImageId $imageId): void
    {
        $sql = ' select num_records from analysis where id = :analysis_id';

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('analysis_id', $analysisId->binary());
        $statement->execute();

        $row = $statement->fetch('assoc');

        if (!$row) {
            return;
        }

        $numRecords = intval($row['num_records']);

        if ($numRecords === 1) {
            $sql = ' delete from analysis where id = :analysis_id';

            $statement = $this->connection->prepare($sql);
            $statement->bindValue('analysis_id', $analysisId->binary());
            $statement->execute();

            return;
        }

        $sql = "
            select num_panels, num_hotspots from image_analysis
            where analysis_id = :analysis_id and image_id = :image_id
        ";

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('analysis_id', $analysisId->binary());
        $statement->bindValue('image_id', $imageId->binary());
        $statement->execute();

        $row = $statement->fetch('assoc');

        if (!$row) {
            return;
        }

        $numPanels = intval($row['num_panels']);
        $numHotspots = intval($row['num_hotspots']);

        $sql = "
            delete from image_analysis
            where analysis_id = :analysis_id
              and image_id = :image_id
        ";

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('analysis_id', $analysisId->binary());
        $statement->bindValue('image_id', $imageId->binary());
        $statement->execute();

        $sql = "
            update analysis 
            set num_records = num_records - 1,
                num_panels = num_panels - :num_record_panels,
                num_hotspots = num_hotspots - :num_record_hotspots
            where id = :analysis_id
        ";

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('num_record_panels', $numPanels);
        $statement->bindValue('num_record_hotspots', $numHotspots);
        $statement->bindValue('analysis_id', $analysisId->binary());
        $statement->execute();
    }

    public function removeByAnalysisId(AnalysisId $analysisId): void
    {
        $sql = 'delete from analysis where id = :analysis_id';

        $statement = $this->connection->prepare($sql);
        $statement->bindValue('analysis_id', $analysisId->binary());
        $statement->execute();
    }

    public function removeAll(): void
    {
        $this->connection->execute('delete from image_analysis');
    }
}
