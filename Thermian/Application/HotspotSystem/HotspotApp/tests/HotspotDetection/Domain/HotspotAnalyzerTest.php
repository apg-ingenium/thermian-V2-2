<?php
declare(strict_types=1);

namespace Hotspot\Test\HotspotDetection\Domain;

use Cake\Database\Connection;
use Cake\Database\Driver\Mysql;
use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\AnalysisId;
use Hotspot\HotspotDataset\Domain\Image\Image;
use Hotspot\HotspotDataset\Domain\Image\ImageId;
use Hotspot\HotspotDataset\Domain\ImageRepository\ImageRepository;
use Hotspot\HotspotDataset\Persistence\ImageRepository\MySQLImageRepository;
use Hotspot\HotspotDetection\Domain\HotspotAnalyzer;
use Hotspot\HotspotResults\Domain\HotspotCsvRepository\HotspotCsvRepository;
use Hotspot\HotspotResults\Domain\HotspotImageRepository\HotspotImageRepository;
use Hotspot\HotspotResults\Persistence\HotspotCsvRepository\MySQLHotspotCsvRepository;
use Hotspot\HotspotResults\Persistence\HotspotImageRepository\MySQLHotspotImageRepository;
use PHPUnit\Framework\TestCase;
use function env;

class HotspotAnalyzerTest extends TestCase
{
    private const FILES = '/app/Thermian/Application/HotspotSystem/HotspotApp/tests/Files/';

    private HotspotAnalyzer $analyzer;
    private ImageRepository $imageRepository;
    private HotspotImageRepository $hotspotImageRepository;
    private HotspotCsvRepository $hotspotCsvRepository;

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

        $this->imageRepository =
            new MySQLImageRepository($connection);

        $this->hotspotImageRepository =
            new MySQLHotspotImageRepository($connection);

        $this->hotspotCsvRepository =
            new MySQLHotspotCsvRepository($connection);

        $this->tearDown();

        $this->analyzer = new HotspotAnalyzer('fake');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->hotspotCsvRepository->removeAll();
        $this->hotspotImageRepository->removeAll();
        $this->imageRepository->removeAll();
    }

    public function testPerformsAHotspotAnalysisAndGeneratesOutputResultFiles(): void
    {
        $imageName = 'input-image.jpg';
        $pathToInputImage = self::FILES . $imageName;
        $hotspotImageId = ImageId::random();
        $analysisId = AnalysisId::random();

        $imageId = ImageId::fromString($hotspotImageId->value());
        $this->imageRepository->save(Image::fromPath($imageId, $pathToInputImage, $imageName));

        $this->analyzer->analyze($analysisId->value(), $hotspotImageId->value());

        $this->assertNotEmpty($this->hotspotCsvRepository->findByRecordId($analysisId, $hotspotImageId));
        $this->assertNotNull($this->hotspotImageRepository->findByCompositeId($analysisId, $hotspotImageId));
    }
}
