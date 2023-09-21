<?php
declare(strict_types=1);

namespace Hotspot\Test\HotspotResults\UseCase\FindHotspotImage;

use Hamcrest\Matchers;
use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\AnalysisId;
use Hotspot\HotspotDataset\Domain\Image\ImageId;
use Hotspot\HotspotResults\Domain\HotspotImageRepository\HotspotImageRepository;
use Hotspot\HotspotResults\UseCase\FindHotspotImage\FindHotspotImageQuery;
use Hotspot\HotspotResults\UseCase\FindHotspotImage\FindHotspotImageUseCase;
use Hotspot\Test\HotspotResults\Domain\HotspotImage\TestHotspotImageBuilder;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;

class FindHotspotImageUseCaseTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private FindHotspotImageUseCase $useCase;
    private MockInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(HotspotImageRepository::class);
        $this->useCase = new FindHotspotImageUseCase(
            $this->repository
        );
    }

    public function testFindsTheOutputImageAssociatedWithAHotspotAnalysis(): void
    {
        $analysisId = AnalysisId::random();
        $imageId = ImageId::random();

        $hotspotImage = TestHotspotImageBuilder::random()
            ->withAnalysisId($analysisId)
            ->withImageId($imageId)
            ->build();

        $this->repository
            ->shouldReceive('findByAnalysisIdImageIdAndName')
            ->with(
                Matchers::equalTo($analysisId),
                Matchers::equalTo($imageId),
                Matchers::equalTo('bounding-boxes')
            )
            ->andReturn($hotspotImage)
            ->once();

        $query = new FindHotspotImageQuery(
            $analysisId->value(),
            $imageId->value(),
        );

        $result = $this->useCase->execute($query);

        $this->assertEquals($hotspotImage, $result);
    }
}
