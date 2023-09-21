<?php
declare(strict_types=1);

namespace Hotspot\Test\HotspotResults\UseCase\FindHotspotCsv;

use Hamcrest\Matchers;
use Hotspot\HotspotResults\Domain\HotspotCsvRepository\HotspotCsvRepository;
use Hotspot\HotspotResults\UseCase\FindHotspotCsv\FindHotspotCsvQuery;
use Hotspot\HotspotResults\UseCase\FindHotspotCsv\FindHotspotCsvUseCase;
use Hotspot\Test\HotspotResults\Domain\HotspotCsv\TestHotspotCsvBuilder;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;

class FindHotspotCsvUseCaseTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private FindHotspotCsvUseCase $useCase;
    private MockInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(HotspotCsvRepository::class);
        $this->useCase = new FindHotspotCsvUseCase(
            $this->repository
        );
    }

    public function testFindsOutputResultsAssociatedWithAHotspotAnalysis(): void
    {
        $hotspotCsv = TestHotspotCsvBuilder::random()->build();
        $analysisId = $hotspotCsv->getAnalysisId();
        $imageId = $hotspotCsv->getImageId();
        $csvName = 'hotspots.csv';

        $this->repository
            ->shouldReceive('findByRecordIdAndName')
            ->with(Matchers::equalTo($analysisId), Matchers::equalTo($imageId), Matchers::equalTo($csvName))
            ->andReturn($hotspotCsv)
            ->once();

        $query = new FindHotspotCsvQuery(
            $analysisId->value(),
            $imageId->value()
        );

        $result = $this->useCase->execute($query);

        $this->assertEquals($hotspotCsv, $result);
    }
}
