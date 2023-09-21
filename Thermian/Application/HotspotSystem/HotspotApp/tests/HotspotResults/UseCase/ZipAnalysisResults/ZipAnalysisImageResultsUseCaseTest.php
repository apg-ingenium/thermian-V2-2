<?php
declare(strict_types=1);

namespace Hotspot\Test\HotspotResults\UseCase\ZipAnalysisResults;

use Cake\Database\Connection;
use Cake\Database\Driver\Mysql;
use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\AnalysisId;
use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\HotspotAnalysisBuilder;
use Hotspot\HotspotAnalysis\Domain\HotspotAnalysisRepository\HotspotAnalysisRepository;
use Hotspot\HotspotAnalysis\Persistence\HotspotAnalysisRepository\AnalysisRecordSummary\AnalysisRecordSummaryRepository;
use Hotspot\HotspotAnalysis\Persistence\HotspotAnalysisRepository\AnalysisSummary\AnalysisSummaryRepository;
use Hotspot\HotspotAnalysis\Persistence\HotspotAnalysisRepository\MySQLHotspotAnalysisRepository;
use Hotspot\HotspotAnalysis\Persistence\HotspotRepository\MySQLHotspotRepository;
use Hotspot\HotspotAnalysis\Persistence\PanelRepository\MySQLPanelRepository;
use Hotspot\HotspotDataset\Domain\Image\ImageId;
use Hotspot\HotspotResults\Domain\HotspotImageRepository\HotspotImageRepository;
use Hotspot\HotspotResults\Persistence\HotspotImageRepository\MySQLHotspotImageRepository;
use Hotspot\HotspotResults\UseCase\ZipAnalysisResults\AnalysisFilesNotFoundException;
use Hotspot\HotspotResults\UseCase\ZipAnalysisResults\DirectoryArchitects\OneImagePerRecordAnalysisDirectoryArchitect;
use Hotspot\HotspotResults\UseCase\ZipAnalysisResults\FileFinders\MySQLAnalysisFileFinder;
use Hotspot\HotspotResults\UseCase\ZipAnalysisResults\ZipAnalysisFilesCommand;
use Hotspot\HotspotResults\UseCase\ZipAnalysisResults\ZipAnalysisFilesUseCase;
use Hotspot\Test\HotspotAnalysis\Domain\HotspotAnalysis\TestHotspotAnalysisRecordBuilder;
use Hotspot\Test\HotspotResults\Domain\HotspotImage\TestHotspotImageBuilder;
use PHPUnit\Framework\TestCase;
use Shared\Domain\Uuid;
use Shared\Utils\Zipper;
use ZipArchive;
use function env;

class ZipAnalysisImageResultsUseCaseTest extends TestCase
{
    private ZipAnalysisFilesUseCase $useCase;
    private HotspotAnalysisRepository $hotspotAnalysisRepository;
    private HotspotImageRepository $hotspotImageRepository;

    protected function setUp(): void
    {
        parent::setUp();

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

        $this->useCase = new ZipAnalysisFilesUseCase(
            new MySQLAnalysisFileFinder($connection, 'output_image'),
            new OneImagePerRecordAnalysisDirectoryArchitect(),
            new Zipper()
        );

        $this->tearDown();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->hotspotAnalysisRepository->removeAll();
        $this->hotspotImageRepository->removeAll();
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        system('rm -rf /app/zipper');
    }

    private function createPathToOutputZip(): string
    {
        $randomValue = Uuid::random()->value();

        return "/app/zipper/${randomValue}";
    }

    public function testZipsAnalysisWithMultipleFiles(): void
    {
        $analysisId = AnalysisId::random();
        $pathToOutputZip = $this->createPathToOutputZip();
        $this->storeAnalysisWithFiles($analysisId, [
            'image-1.jpg' => ['bounding-boxes-1.jpg' => 'content 1'],
            'image-2.png' => ['bounding-boxes-2.png' => 'content 2'],
            'image-3.jpeg' => [],
        ]);

        $this->zip($analysisId, $pathToOutputZip);

        $zip = new ZipArchive();
        $zip->open($pathToOutputZip);
        $this->assertEquals('content 1', $zip->getFromName('image-1.jpg'));
        $this->assertEquals('content 2', $zip->getFromName('image-2.png'));
        $this->assertFalse($zip->getFromName('image-3.jpeg'));
    }

    public function testDoesNotZipAnalysisWithNoFiles(): void
    {
        $analysisId = AnalysisId::random();
        $pathToOutputZip = $this->createPathToOutputZip();
        $this->storeAnalysisWithFiles($analysisId, []);

        $this->expectException(AnalysisFilesNotFoundException::class);
        $this->zip($analysisId, $pathToOutputZip);
    }

    public function testDoesNotZipNonExistentAnalysis(): void
    {
        $pathToOutputZip = $this->createPathToOutputZip();
        $this->expectException(AnalysisFilesNotFoundException::class);
        $this->zip(AnalysisId::random(), $pathToOutputZip);
    }

    private function zip(AnalysisId $analysisId, string $pathToOutputZip): void
    {
        $this->useCase->execute(
            new ZipAnalysisFilesCommand($analysisId->value(), $pathToOutputZip)
        );
    }

    /** @param array<array<string>> $fileStructure */
    private function storeAnalysisWithFiles(AnalysisId $analysisId, array $fileStructure): void
    {
        $analysis = HotspotAnalysisBuilder
            ::hotspotAnalysis()
            ->withTarget('Any Dataset')
            ->withAnalysisId($analysisId);

        foreach ($fileStructure as $recordName => $recordFiles) {
            $imageId = ImageId::random();
            $analysis->withRecord(
                TestHotspotAnalysisRecordBuilder
                    ::hotspotAnalysisRecord()
                    ->withAnalysisId($analysisId)
                    ->withImageId($imageId)
                    ->withImageName($recordName)
                    ->build()
            );

            foreach ($recordFiles as $fileName => $fileContent) {
                $this->hotspotImageRepository->save(
                    TestHotspotImageBuilder
                        ::random()
                        ->withAnalysisId($analysisId)
                        ->withImageId($imageId)
                        ->withName($fileName)
                        ->withContent($fileContent)
                        ->build()
                );
            }
        }

        $this->hotspotAnalysisRepository->saveAnalysis($analysis->build());
    }
}
