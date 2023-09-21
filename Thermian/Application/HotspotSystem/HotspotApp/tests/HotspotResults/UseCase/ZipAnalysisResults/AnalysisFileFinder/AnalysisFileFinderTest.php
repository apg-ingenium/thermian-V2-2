<?php
declare(strict_types=1);

namespace Hotspot\Test\HotspotResults\UseCase\ZipAnalysisResults\AnalysisFileFinder;

use Generator;
use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\AnalysisId;
use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\HotspotAnalysisBuilder;
use Hotspot\HotspotAnalysis\Domain\HotspotAnalysisRepository\HotspotAnalysisRepository;
use Hotspot\HotspotDataset\Domain\Image\ImageId;
use Hotspot\HotspotResults\UseCase\ZipAnalysisResults\AnalysisFileFinder;
use Hotspot\Test\HotspotAnalysis\Domain\HotspotAnalysis\TestHotspotAnalysisRecordBuilder;
use PHPUnit\Framework\TestCase;

abstract class AnalysisFileFinderTest extends TestCase
{
    private HotspotAnalysisRepository $hotspotAnalysisRepository;
    private AnalysisFileFinder $analysisCsvFinder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->hotspotAnalysisRepository = $this->getAnalysisRepository();
        $this->analysisCsvFinder = $this->getFileFinder();
        $this->tearDown();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->hotspotAnalysisRepository->removeAll();
    }

    abstract protected function getAnalysisRepository(): HotspotAnalysisRepository;

    abstract protected function getFileFinder(): AnalysisFileFinder;

    abstract protected function storeFile(AnalysisId $analysisId, ImageId $imageId, string $csvName, string $csvContent): void;

    public function testFindsAnalysisWithNoRecordsAndNoFiles(): void
    {
        $analysisId = AnalysisId::random();
        $this->storeAnalysisWithFiles($analysisId, []);

        $csvResults = $this->analysisCsvFinder->find($analysisId);
        $this->assertFalse($csvResults->valid());
    }

    public function testFindsAnalysisWithOneRecordAndAndNoFiles(): void
    {
        $analysisId = AnalysisId::random();
        $this->storeAnalysisWithFiles($analysisId, ['record-1.jpeg' => []]);
        $csvResults = $this->analysisCsvFinder->find($analysisId);
        $this->assertFilesMatch([], $csvResults);
    }

    public function testFindsAnalysisWithOneRecordAndAnOneFile(): void
    {
        $analysisId = AnalysisId::random();
        $analysisFileSpec = ['record-1.jpeg' => ['csv-1.csv' => 'content 1']];

        $this->storeAnalysisWithFiles($analysisId, $analysisFileSpec);
        $csvResults = $this->analysisCsvFinder->find($analysisId);
        $this->assertFilesMatch($analysisFileSpec, $csvResults);
    }

    public function testFindsAnalysisWithOneRecordAndMultipleFiles(): void
    {
        $analysisId = AnalysisId::random();
        $analysisFileSpec = [
            'record-1.jpeg' => [
                'csv-1.csv' => 'content 1',
                'csv-2.csv' => 'content 2',
                'csv-3.csv' => 'content 3',
            ],
        ];

        $this->storeAnalysisWithFiles($analysisId, $analysisFileSpec);
        $csvResults = $this->analysisCsvFinder->find($analysisId);
        $this->assertFilesMatch($analysisFileSpec, $csvResults);
    }

    public function testFindsAnalysisWithMultipleRecordsAndNoFiles(): void
    {
        $analysisId = AnalysisId::random();
        $analysisFileSpec = ['record-1.jpeg' => [], 'record-2.jpg' => [], 'record-3.png' => []];

        $this->storeAnalysisWithFiles($analysisId, $analysisFileSpec);
        $csvResults = $this->analysisCsvFinder->find($analysisId);
        $this->assertFilesMatch([], $csvResults);
    }

    public function testFindsAnalysisWithMultipleRecordsAndOneFilePerRecord(): void
    {
        $analysisId = AnalysisId::random();
        $analysisFileSpec = [
            'record-1.jpeg' => ['file-1.csv' => 'content 1'],
            'record-2.jpg' => ['file-2.csv' => 'content 2'],
            'record-3.png' => ['file-2.csv' => 'content 3'],
        ];

        $this->storeAnalysisWithFiles($analysisId, $analysisFileSpec);
        $csvResults = $this->analysisCsvFinder->find($analysisId);
        $this->assertFilesMatch($analysisFileSpec, $csvResults);
    }

    public function testFindsAnalysisWithMultipleRecordsAndMultipleFilePerRecord(): void
    {
        $analysisId = AnalysisId::random();
        $analysisFileSpec = [
            'record-1.jpeg' => [
                'file-1.csv' => 'content 1',
                'file-2.csv' => 'content 2',
                'file-3.csv' => 'content 3',
            ],
            'record-2.jpg' => [
                'file-4.csv' => 'content 4',
                'file-5.csv' => 'content 5',
                'file-6.csv' => 'content 6',
            ],
            'record-3.png' => [
                'file-7.csv' => 'content 7',
                'file-8.csv' => 'content 8',
                'file-9.csv' => 'content 9',
            ],
        ];

        $this->storeAnalysisWithFiles($analysisId, $analysisFileSpec);
        $csvResults = $this->analysisCsvFinder->find($analysisId);
        $this->assertFilesMatch($analysisFileSpec, $csvResults);
    }

    public function testReturnsNoFilesForNonExistentAnalysis(): void
    {
        $csvResults = $this->analysisCsvFinder->find(AnalysisId::random());
        $this->assertFilesMatch([], $csvResults);
    }

    public function testDoesNotReturnFilesFromAnotherAnalysisWithCommonRecords(): void
    {
        $recordId = ImageId::random()->value();
        $analysisId1 = AnalysisId::random();
        $analysisId2 = AnalysisId::random();

        $analysisCollectionSpec = [
            $analysisId1->value() => [
                $recordId => [
                    'name' => 'record-1.jpeg',
                    'files' => ['file-1.csv' => 'content 1', 'file-2.csv' => 'content 2'],
                ],
            ],
            $analysisId2->value() => [
                $recordId => ['name' => 'record-1.jpeg',
                    'files' => ['file-3.csv' => 'content 3'],
                ],
            ],
        ];

        $expectedFiles = [
            'record-1.jpeg' => [
                'file-1.csv' => 'content 1',
                'file-2.csv' => 'content 2',
            ],
        ];

        $this->storeAnalysisCollection($analysisCollectionSpec);
        $csvResults = $this->analysisCsvFinder->find($analysisId1);
        $this->assertFilesMatch($expectedFiles, $csvResults);
    }

    /** @param array<string, array<string, string>> $analysisCsvSpec */
    private function storeAnalysisWithFiles(AnalysisId $analysisId, array $analysisCsvSpec): void
    {
        $analysis = HotspotAnalysisBuilder
            ::hotspotAnalysis()
            ->withTarget('Any Dataset')
            ->withAnalysisId($analysisId);

        foreach ($analysisCsvSpec as $imageName => $recordCsvs) {
            $imageId = ImageId::random();
            $analysis->withRecord(
                TestHotspotAnalysisRecordBuilder
                    ::hotspotAnalysisRecord()
                    ->withAnalysisId($analysisId)
                    ->withImageId($imageId)
                    ->withImageName($imageName)
                    ->build()
            );

            foreach ($recordCsvs as $csvName => $csvContent) {
                $this->storeFile($analysisId, $imageId, $csvName, $csvContent);
            }
        }

        $this->hotspotAnalysisRepository->saveAnalysis($analysis->build());
    }

    /** @param array<string, array<string, array<string, mixed>>> $analysisCollectionSpec */
    private function storeAnalysisCollection(array $analysisCollectionSpec): void
    {
        foreach ($analysisCollectionSpec as $analysisId => $analysisSpec) {
            $analysisId = AnalysisId::fromString($analysisId);
            $analysis = HotspotAnalysisBuilder
                ::hotspotAnalysis()
                ->withTarget('Any Dataset')
                ->withAnalysisId($analysisId);

            foreach ($analysisSpec as $recordId => $recordSpec) {
                $imageId = ImageId::fromString($recordId);
                $analysis->withRecord(
                    TestHotspotAnalysisRecordBuilder
                        ::hotspotAnalysisRecord()
                        ->withAnalysisId($analysisId)
                        ->withImageId($imageId)
                        ->withImageName($recordSpec['name'])
                        ->build()
                );

                foreach ($recordSpec['files'] as $csvName => $csvContent) {
                    $this->storeFile($analysisId, $imageId, $csvName, $csvContent);
                }
            }

            $this->hotspotAnalysisRepository->saveAnalysis($analysis->build());
        }
    }

    /**
     * @param array<string, array<string, string>> $expectedFiles
     * @param Generator<\Hotspot\HotspotResults\UseCase\ZipAnalysisResults\AnalysisRecordFile> $fileResults
     */
    public function assertFilesMatch(array $expectedFiles, Generator $fileResults): void
    {
        $results = [];
        foreach ($fileResults as $result) {
            $recordName = $result->getRecordName();
            $fileName = $result->getName();
            $fileContent = $result->getContent();

            if (!array_key_exists($recordName, $results)) {
                $results[$recordName] = [];
            }

            $results[$recordName][$fileName] = $fileContent;
        }

        $this->assertEquals($expectedFiles, $results);
    }
}
