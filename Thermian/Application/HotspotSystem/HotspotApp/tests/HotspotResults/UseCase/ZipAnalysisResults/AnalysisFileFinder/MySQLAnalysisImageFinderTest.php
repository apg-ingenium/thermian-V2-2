<?php
declare(strict_types=1);

namespace Hotspot\Test\HotspotResults\UseCase\ZipAnalysisResults\AnalysisFileFinder;

use Cake\Database\Connection;
use Cake\Database\Driver\Mysql;
use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\AnalysisId;
use Hotspot\HotspotAnalysis\Domain\HotspotAnalysisRepository\HotspotAnalysisRepository;
use Hotspot\HotspotAnalysis\Persistence\HotspotAnalysisRepository\AnalysisRecordSummary\AnalysisRecordSummaryRepository;
use Hotspot\HotspotAnalysis\Persistence\HotspotAnalysisRepository\AnalysisSummary\AnalysisSummaryRepository;
use Hotspot\HotspotAnalysis\Persistence\HotspotAnalysisRepository\MySQLHotspotAnalysisRepository;
use Hotspot\HotspotAnalysis\Persistence\HotspotRepository\MySQLHotspotRepository;
use Hotspot\HotspotAnalysis\Persistence\PanelRepository\MySQLPanelRepository;
use Hotspot\HotspotDataset\Domain\Image\ImageId;
use Hotspot\HotspotResults\Domain\HotspotImageRepository\HotspotImageRepository;
use Hotspot\HotspotResults\Persistence\HotspotImageRepository\MySQLHotspotImageRepository;
use Hotspot\HotspotResults\UseCase\ZipAnalysisResults\AnalysisFileFinder;
use Hotspot\HotspotResults\UseCase\ZipAnalysisResults\FileFinders\MySQLAnalysisFileFinder;
use Hotspot\Test\HotspotResults\Domain\HotspotImage\TestHotspotImageBuilder;

class MySQLAnalysisImageFinderTest extends AnalysisFileFinderTest
{
    private HotspotAnalysisRepository $hotspotAnalysisRepository;
    private HotspotImageRepository $hotspotImageRepository;
    private AnalysisFileFinder $analysisCsvFinder;

    protected function setUp(): void
    {
        $connection = new Connection([
            'driver' => new Mysql([
                'host' => env('MYSQL_HOST'),
                'username' => env('MYSQL_USER'),
                'password' => env('MYSQL_PASSWORD'),
                'database' => env('MYSQL_DATABASE'),
            ]),
        ]);

        $this->hotspotAnalysisRepository =
            new MySQLHotspotAnalysisRepository(
                new AnalysisSummaryRepository($connection),
                new AnalysisRecordSummaryRepository($connection),
                new MySQLPanelRepository($connection),
                new MySQLHotspotRepository($connection)
            );

        $this->hotspotImageRepository = new MySQLHotspotImageRepository($connection);
        $this->analysisCsvFinder = new MySQLAnalysisFileFinder($connection, 'output_image');
        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->hotspotImageRepository->removeAll();
    }

    public function getAnalysisRepository(): HotspotAnalysisRepository
    {
        return $this->hotspotAnalysisRepository;
    }

    public function getFileFinder(): AnalysisFileFinder
    {
        return $this->analysisCsvFinder;
    }

    public function storeFile(AnalysisId $analysisId, ImageId $imageId, string $csvName, string $csvContent): void
    {
        $this->hotspotImageRepository->save(
            TestHotspotImageBuilder
                ::random()
                ->withAnalysisId($analysisId)
                ->withImageId($imageId)
                ->withName($csvName)
                ->withContent($csvContent)
                ->build()
        );
    }
}
