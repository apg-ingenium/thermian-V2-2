<?php
declare(strict_types=1);

namespace Hotspot\Test\HotspotAnalysis\Domain\HotspotCsvParser;

use Hotspot\HotspotAnalysis\Domain\GpsCoordinates\GpsCoordinatesBuilder;
use Hotspot\HotspotAnalysis\Domain\Hotspot\HotspotBuilder;
use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\AnalysisId;
use Hotspot\HotspotAnalysis\Domain\HotspotAnalysisCsvParser\HotspotAnalysisCsvParser;
use Hotspot\HotspotAnalysis\Domain\Panel\PanelBuilder;
use Hotspot\HotspotDataset\Domain\Image\ImageId;
use Hotspot\Test\HotspotAnalysis\Domain\HotspotAnalysis\TestHotspotAnalysisRecordBuilder;
use Hotspot\Test\HotspotResults\Domain\HotspotCsv\TestHotspotCsvBuilder;
use PHPUnit\Framework\TestCase;

class HotspotAnalysisCsvParserTest extends TestCase
{
    private const FILES = 'Thermian/Application/HotspotSystem/HotspotApp/tests/Files/';
    private HotspotAnalysisCsvParser $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new HotspotAnalysisCsvParser();
    }

    public function testParsesHotspotAnalysisRecordCsvResultsSuccessfully(): void
    {
        $pathToHotspotCsv = self::FILES . 'hotspots.csv';
        $pathToPanelCsv = self::FILES . 'panels.csv';
        $pathToGpsCsv = self::FILES . 'gps.csv';
        $analysisId = AnalysisId::random();
        $imageId = ImageId::random();
        $imageName = 'image.png';

        $outputCsvs = [
            'hotspots.csv' => TestHotspotCsvBuilder::create()
                ->withAnalysisId($analysisId)
                ->withImageId($imageId)
                ->fromPath($pathToHotspotCsv)
                ->build(),
            'panels.csv' => TestHotspotCsvBuilder::create()
                ->withAnalysisId($analysisId)
                ->withImageId($imageId)
                ->fromPath($pathToPanelCsv)
                ->build(),
            'gps.csv' => TestHotspotCsvBuilder::create()
                ->withAnalysisId($analysisId)
                ->withImageId($imageId)
                ->fromPath($pathToGpsCsv)
                ->build(),
        ];

        $expectedAnalysisRecord =
            TestHotspotAnalysisRecordBuilder::hotspotAnalysisRecord()
                ->withAnalysisId($analysisId)
                ->withImageId($imageId)
                ->withImageName($imageName)
                ->withHotspot(
                    HotspotBuilder::hotspot()
                        ->withIndex(1)
                        ->withPanelIndex(1)
                        ->withScore(0.94)
                        ->withYMin(108)
                        ->withXMin(202)
                        ->withYMax(117)
                        ->withXMax(210)
                        ->build()
                )
                ->withHotspot(
                    HotspotBuilder::hotspot()
                        ->withIndex(2)
                        ->withPanelIndex(1)
                        ->withScore(0.89)
                        ->withYMin(88)
                        ->withXMin(224)
                        ->withYMax(97)
                        ->withXMax(232)
                        ->build()
                )
                ->withHotspot(
                    HotspotBuilder::hotspot()
                        ->withIndex(3)
                        ->withPanelIndex(1)
                        ->withScore(0.88)
                        ->withYMin(94)
                        ->withXMin(232)
                        ->withYMax(103)
                        ->withXMax(239)
                        ->build()
                )
                ->withHotspot(
                    HotspotBuilder::hotspot()
                        ->withIndex(1)
                        ->withPanelIndex(2)
                        ->withScore(0.92)
                        ->withYMin(354)
                        ->withXMin(299)
                        ->withYMax(363)
                        ->withXMax(307)
                        ->build()
                )
                ->withHotspot(
                    HotspotBuilder::hotspot()
                        ->withIndex(2)
                        ->withPanelIndex(2)
                        ->withScore(0.90)
                        ->withYMin(223)
                        ->withXMin(320)
                        ->withYMax(231)
                        ->withXMax(327)
                        ->build()
                )
                ->withHotspot(
                    HotspotBuilder::hotspot()
                        ->withIndex(1)
                        ->withPanelIndex(6)
                        ->withScore(0.87)
                        ->withYMin(191)
                        ->withXMin(455)
                        ->withYMax(200)
                        ->withXMax(464)
                        ->build()
                )
                ->withHotspot(
                    HotspotBuilder::hotspot()
                        ->withIndex(2)
                        ->withPanelIndex(6)
                        ->withScore(0.84)
                        ->withYMin(212)
                        ->withXMin(460)
                        ->withYMax(222)
                        ->withXMax(467)
                        ->build()
                )
                ->withHotspot(
                    HotspotBuilder::hotspot()
                        ->withIndex(3)
                        ->withPanelIndex(6)
                        ->withScore(0.84)
                        ->withYMin(307)
                        ->withXMin(442)
                        ->withYMax(315)
                        ->withXMax(449)
                        ->build()
                )
                ->withHotspot(
                    HotspotBuilder::hotspot()
                        ->withIndex(4)
                        ->withPanelIndex(6)
                        ->withScore(0.80)
                        ->withYMin(244)
                        ->withXMin(456)
                        ->withYMax(253)
                        ->withXMax(463)
                        ->build()
                )
                ->withHotspot(
                    HotspotBuilder::hotspot()
                        ->withIndex(5)
                        ->withPanelIndex(6)
                        ->withScore(0.72)
                        ->withYMin(213)
                        ->withXMin(459)
                        ->withYMax(221)
                        ->withXMax(466)
                        ->build()
                )
                ->withPanel(
                    PanelBuilder::panel()
                        ->withIndex(1)
                        ->withScore(1.00)
                        ->withYMin(0)
                        ->withXMin(181)
                        ->withYMax(185)
                        ->withXMax(245)
                        ->build()
                )
                ->withPanel(
                    PanelBuilder::panel()
                        ->withIndex(2)
                        ->withScore(1.00)
                        ->withYMin(185)
                        ->withXMin(290)
                        ->withYMax(408)
                        ->withXMax(353)
                        ->build()
                )
                ->withPanel(
                    PanelBuilder::panel()
                        ->withIndex(3)
                        ->withScore(1.00)
                        ->withYMin(348)
                        ->withXMin(404)
                        ->withYMax(512)
                        ->withXMax(459)
                        ->build()
                )
                ->withPanel(
                    PanelBuilder::panel()
                        ->withIndex(4)
                        ->withScore(1.00)
                        ->withYMin(1)
                        ->withXMin(301)
                        ->withYMax(191)
                        ->withXMax(361)
                        ->build()
                )
                ->withPanel(
                    PanelBuilder::panel()
                        ->withIndex(5)
                        ->withScore(1.00)
                        ->withYMin(418)
                        ->withXMin(523)
                        ->withYMax(512)
                        ->withXMax(571)
                        ->build()
                )
                ->withPanel(
                    PanelBuilder::panel()
                        ->withIndex(6)
                        ->withScore(1.00)
                        ->withYMin(128)
                        ->withXMin(413)
                        ->withYMax(352)
                        ->withXMax(469)
                        ->build()
                )
                ->withGpsCoordinates(
                    GpsCoordinatesBuilder::gpsCoordinates()
                        ->withLatitudeDegrees(38)
                        ->withLatitudeMinutes(43)
                        ->withLatitudeSeconds(22.853)
                        ->withLatitudeDirection('N')
                        ->withLongitudeDegrees(0)
                        ->withLongitudeMinutes(43)
                        ->withLongitudeSeconds(44.723)
                        ->withLongitudeDirection('W')
                        ->build()
                )
                ->build();

        $analysisRecord = $this->parser->parseAnalysisRecord($analysisId, $imageId, $imageName, $outputCsvs);
        $this->assertObjectEquals($expectedAnalysisRecord, $analysisRecord);
    }
}
