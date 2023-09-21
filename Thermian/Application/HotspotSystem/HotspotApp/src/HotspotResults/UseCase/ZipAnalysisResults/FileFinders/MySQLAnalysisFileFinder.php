<?php
declare(strict_types=1);

namespace Hotspot\HotspotResults\UseCase\ZipAnalysisResults\FileFinders;

use Cake\Database\Connection;
use Generator;
use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\AnalysisId;
use Hotspot\HotspotResults\UseCase\ZipAnalysisResults\AnalysisFileFinder;
use Hotspot\HotspotResults\UseCase\ZipAnalysisResults\AnalysisRecordFile;

class MySQLAnalysisFileFinder implements AnalysisFileFinder
{
    private Connection $connection;
    private string $tableName;

    public function __construct(Connection $connection, string $tableName)
    {
        $this->connection = $connection;
        $this->tableName = $tableName;
    }

    /** @return \Generator<\Hotspot\HotspotResults\UseCase\ZipAnalysisResults\AnalysisRecordFile> */
    public function find(AnalysisId $analysisId): Generator
    {
        $statement = $this->connection->newQuery()
            ->disableBufferedResults()
            ->select('ia.image_name, file.name, file.content')
            ->from(['ia' => 'image_analysis'])
            ->join([
                'file' => [
                    'table' => $this->tableName,
                    'conditions' => [
                        'ia.image_id = file.image_id',
                        'ia.analysis_id = file.analysis_id',
                    ],
                ],
            ])
            ->where(['ia.analysis_id' => $analysisId->binary()])
            ->execute();

        while ($row = $statement->fetch('assoc')) {
            yield AnalysisRecordFile::create(
                $row['image_name'],
                $row['name'],
                $row['content']
            );
        }
    }
}
