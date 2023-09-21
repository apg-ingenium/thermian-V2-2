<?php
declare(strict_types=1);

namespace Hotspot\Test\HotspotAnalysis\Persistence\HotspotAnalysisRepository;

use Cake\Database\Connection;
use Cake\Database\Driver\Mysql;
use Hotspot\HotspotAnalysis\Domain\HotspotAnalysisRepository\HotspotAnalysisRepository;
use Hotspot\HotspotAnalysis\Persistence\HotspotAnalysisRepository\AnalysisRecordSummary\AnalysisRecordSummaryRepository;
use Hotspot\HotspotAnalysis\Persistence\HotspotAnalysisRepository\AnalysisSummary\AnalysisSummaryRepository;
use Hotspot\HotspotAnalysis\Persistence\HotspotAnalysisRepository\MySQLHotspotAnalysisRepository;
use Hotspot\HotspotAnalysis\Persistence\HotspotRepository\MySQLHotspotRepository;
use Hotspot\HotspotAnalysis\Persistence\PanelRepository\MySQLPanelRepository;
use Hotspot\Test\HotspotAnalysis\Domain\HotspotAnalysisRepository\HotspotAnalysisRepositoryTest;

class MySQLHotspotAnalysisRepositoryTest extends HotspotAnalysisRepositoryTest
{
    protected function getRepository(): HotspotAnalysisRepository
    {
        $connection = new Connection([
            'driver' => new Mysql([
                'host' => env('MYSQL_HOST'),
                'username' => env('MYSQL_USER'),
                'password' => env('MYSQL_PASSWORD'),
                'database' => env('MYSQL_DATABASE'),
            ]),
        ]);

        return new MySQLHotspotAnalysisRepository(
            new AnalysisSummaryRepository($connection),
            new AnalysisRecordSummaryRepository($connection),
            new MySQLPanelRepository($connection),
            new MySQLHotspotRepository($connection)
        );
    }
}
