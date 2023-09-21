<?php
declare(strict_types=1);

namespace Hotspot\Test\HotspotResults\Domain\HotspotImageRepository;

use Cake\Database\Connection;
use Cake\Database\Driver\Mysql;
use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\AnalysisId;
use Hotspot\HotspotAnalysis\Persistence\HotspotAnalysisRepository\AnalysisRecordSummary\AnalysisRecordSummaryRepository;
use Hotspot\HotspotAnalysis\Persistence\HotspotAnalysisRepository\AnalysisSummary\AnalysisSummaryRepository;
use Hotspot\HotspotDataset\Domain\Image\ImageId;
use Hotspot\HotspotResults\Domain\HotspotImageRepository\HotspotImageRepository;
use Hotspot\Test\HotspotAnalysis\Persistence\HotspotAnalysisRepository\AnalysisRecordSummary\TestAnalysisRecordSummaryBuilder;
use Hotspot\Test\HotspotAnalysis\Persistence\HotspotAnalysisRepository\AnalysisSummary\TestAnalysisSummaryBuilder;
use Hotspot\Test\HotspotResults\Domain\HotspotImage\TestHotspotImageBuilder;
use PHPUnit\Framework\TestCase;
use function env;

abstract class HotspotImageRepositoryTest extends TestCase
{
    private HotspotImageRepository $repository;
    private AnalysisRecordSummaryRepository $imageAnalysisRepository;
    private AnalysisSummaryRepository $analysisDetailsRepository;
    private AnalysisId $analysisId;
    private ImageId $imageId;

    abstract protected function getRepository(): HotspotImageRepository;

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
        $this->repository = $this->getRepository();
        $this->imageAnalysisRepository = new AnalysisRecordSummaryRepository($connection);
        $this->analysisDetailsRepository = new AnalysisSummaryRepository($connection);
        $this->tearDown();

        $this->analysisId = AnalysisId::random();
        $this->imageId = ImageId::random();

        $analysisDetails =
            TestAnalysisSummaryBuilder::random()
            ->withAnalysisId($this->analysisId)
            ->build();

        $imageAnalysis =
            TestAnalysisRecordSummaryBuilder::random()
                ->withAnalysisId($this->analysisId)
                ->withImageId($this->imageId)
                ->build();

        $this->analysisDetailsRepository->save($analysisDetails);
        $this->imageAnalysisRepository->save($imageAnalysis);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->repository->removeAll();
        $this->imageAnalysisRepository->removeAll();
        $this->analysisDetailsRepository->removeAll();
    }

    public function testStoresMultipleImagesForAnExistentRecordId(): void
    {
        $firstImage =
            TestHotspotImageBuilder::random()
                ->withAnalysisId($this->analysisId)
                ->withImageId($this->imageId)
                ->withName('first_image.png')
                ->build();

        $secondImage =
            TestHotspotImageBuilder::random()
                ->withAnalysisId($this->analysisId)
                ->withImageId($this->imageId)
                ->withName('second_image.png')
                ->build();

        $this->repository->save($firstImage);
        $this->repository->save($secondImage);

        $this->assertTrue($this->repository
            ->containsCompositeId($this->analysisId, $this->imageId));

        $storedImages = $this->repository
            ->findByCompositeId($this->analysisId, $this->imageId);

        $this->assertNotEmpty($storedImages);

        $this->assertTrue(
            ($firstImage->equals($storedImages[0]) && $secondImage->equals($storedImages[1]))
            || ($firstImage->equals($storedImages[1]) && $secondImage->equals($storedImages[0]))
        );
    }

    public function testRetrievesExistentImagesByRecordId(): void
    {
        $hotspotImage =
            TestHotspotImageBuilder::random()
                ->withAnalysisId($this->analysisId)
                ->withImageId($this->imageId)
                ->build();

        $this->repository->save($hotspotImage);
        $storedImages = $this->repository
            ->findByCompositeId($this->analysisId, $this->imageId);

        $this->assertNotEmpty($storedImages);
        $this->assertEquals(1, count($storedImages));
        $this->assertObjectEquals($hotspotImage, $storedImages[0]);
    }

    public function testDoesNotRetrieveNonExistentImagesByRecordId(): void
    {
        $this->analysisId = AnalysisId::random();
        $this->imageId = ImageId::random();
        $hotspotImages = $this->repository
            ->findByCompositeId($this->analysisId, $this->imageId);
        $this->assertEmpty($hotspotImages);
    }

    public function testContainsTheRecordIdOfExistentImages(): void
    {
        $hotspotImage =
            TestHotspotImageBuilder::random()
                ->withAnalysisId($this->analysisId)
                ->withImageId($this->imageId)
                ->build();

        $this->repository->save($hotspotImage);

        $this->assertTrue($this->repository
            ->containsCompositeId($this->analysisId, $this->imageId));
    }

    public function testDoesNotContainTheRecordIdOfNonExistentImages(): void
    {
        $this->analysisId = AnalysisId::random();
        $this->imageId = ImageId::random();
        $this->assertFalse($this->repository
            ->containsCompositeId($this->analysisId, $this->imageId));
    }

    public function testRemovesExistentImagesByRecordId(): void
    {
        $hotspotImage =
            TestHotspotImageBuilder::random()
                ->withAnalysisId($this->analysisId)
                ->withImageId($this->imageId)
                ->build();

        $this->repository->save($hotspotImage);
        $this->repository->removeByRecordId($this->analysisId, $this->imageId);

        $this->assertFalse($this->repository
            ->containsCompositeId($this->analysisId, $this->imageId));

        $this->assertEmpty($this->repository
            ->findByCompositeId($this->analysisId, $this->imageId));
    }

    public function testRemovesNonExistentImagesByRecordId(): void
    {
        $this->analysisId = AnalysisId::random();
        $this->imageId = ImageId::random();
        $this->repository->removeByRecordId($this->analysisId, $this->imageId);

        $this->assertFalse($this->repository
            ->containsCompositeId($this->analysisId, $this->imageId));

        $this->assertEmpty($this->repository
            ->findByCompositeId($this->analysisId, $this->imageId));
    }

    public function testRemovesAllImages(): void
    {
        $hotspotImages = [];

        for ($i = 0; $i < 3; $i++) {
            $imageId = ImageId::random();

            $imageAnalysis = TestAnalysisRecordSummaryBuilder::random()
                ->withAnalysisId($this->analysisId)
                ->withImageId($imageId)
                ->build();

            $hotspotImage = TestHotspotImageBuilder::random()
                ->withAnalysisId($this->analysisId)
                ->withImageId($imageId)
                ->build();

            $this->imageAnalysisRepository->save($imageAnalysis);
            $this->repository->save($hotspotImage);

            $hotspotImages[] = $hotspotImage;
        }

        $this->repository->removeAll();

        foreach ($hotspotImages as $image) {
            $this->analysisId = $image->getAnalysisId();
            $this->imageId = $image->getImageId();

            $this->assertFalse($this->repository
                ->containsCompositeId($this->analysisId, $this->imageId));

            $this->assertEmpty($this->repository
                ->findByCompositeId($this->analysisId, $this->imageId));
        }
    }
}
