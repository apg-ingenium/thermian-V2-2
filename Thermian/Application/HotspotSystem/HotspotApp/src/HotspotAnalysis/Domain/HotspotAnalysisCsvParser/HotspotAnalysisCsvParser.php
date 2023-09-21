<?php
declare(strict_types=1);

namespace Hotspot\HotspotAnalysis\Domain\HotspotAnalysisCsvParser;

use Hotspot\HotspotAnalysis\Domain\GpsCoordinates\GpsCoordinatesBuilder;
use Hotspot\HotspotAnalysis\Domain\Hotspot\HotspotBuilder;
use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\AnalysisId;
use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\HotspotAnalysis;
use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\HotspotAnalysisBuilder;
use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\HotspotAnalysisRecord;
use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\HotspotAnalysisRecordBuilder;
use Hotspot\HotspotAnalysis\Domain\Panel\PanelBuilder;
use Hotspot\HotspotDataset\Domain\Image\ImageId;
use Hotspot\HotspotResults\Domain\HotspotCsv\HotspotCsv;
use RuntimeException;

class HotspotAnalysisCsvParser
{
    /**
     * @param array<string> $imageNames
     * @param array<array<\Hotspot\HotspotResults\Domain\HotspotCsv\HotspotCsv>> $analysisCsvResults
     */
    public function parseAnalysis(
        AnalysisId $analysisId,
        string $analysisTarget,
        array $imageNames,
        array $analysisCsvResults
    ): HotspotAnalysis {
        $hotspotAnalysis = HotspotAnalysisBuilder
            ::hotspotAnalysis()
            ->withAnalysisId($analysisId)
            ->withTarget($analysisTarget);

        foreach ($analysisCsvResults as $imageId => $recordCsvResults) {
            $hotspotAnalysis->withRecord(
                $this->parseAnalysisRecord(
                    $analysisId,
                    ImageId::fromString($imageId),
                    $imageNames[$imageId],
                    $recordCsvResults
                )
            );
        }

        return $hotspotAnalysis->build();
    }

    /** @param array<\Hotspot\HotspotResults\Domain\HotspotCsv\HotspotCsv> $files */
    public function parseAnalysisRecord(AnalysisId $analysisId, ImageId $imageId, string $imageName, array $files): HotspotAnalysisRecord
    {
        $record = HotspotAnalysisRecordBuilder
            ::hotspotAnalysisRecord()
            ->withAnalysisId($analysisId)
            ->withImageId($imageId)
            ->withImageName($imageName);

        $panelCsv = $files['panels.csv'] ?? null;
        if (!is_null($panelCsv)) {
            $this->parsePanelCsv($panelCsv, $record);
        }

        $hotspotCsv = $files['hotspots.csv'] ?? null;
        if (!is_null($hotspotCsv)) {
            $this->parseHotspotCsv($hotspotCsv, $record);
        }

        $gpsCsv = $files['gps.csv'] ?? null;
        if (!is_null($gpsCsv)) {
            $this->parseGpsCsv($gpsCsv, $record);
        }

        return $record->build();
    }

    private function parsePanelCsv(HotspotCsv $panelCsv, HotspotAnalysisRecordBuilder $record): void
    {
        $panelCsv = $panelCsv->getStream();

        $header = fgetcsv($panelCsv);
        if ($header === false) {
            throw new RuntimeException(
                'Panel csv header is missing'
            );
        }

        while (($row = fgetcsv($panelCsv)) !== false) {
            $record->withPanel(
                PanelBuilder::panel()
                    ->withIndex(intval($row[0]))
                    ->withScore(floatval($row[1]))
                    ->withYMin(intval($row[2]))
                    ->withXMin(intval($row[3]))
                    ->withYMax(intval($row[4]))
                    ->withXMax(intval($row[5]))
                    ->build()
            );
        }

        fclose($panelCsv);
    }

    private function parseHotspotCsv(HotspotCsv $hotspotCsv, HotspotAnalysisRecordBuilder $record): void
    {
        $hotspotCsv = $hotspotCsv->getStream();

        $header = fgetcsv($hotspotCsv);
        if ($header === false) {
            throw new RuntimeException(
                'Hotspot csv header is missing'
            );
        }

        while (($row = fgetcsv($hotspotCsv)) !== false) {
            $record->withHotspot(
                HotspotBuilder::hotspot()
                    ->withIndex(intval($row[0]))
                    ->withPanelIndex(intval($row[1]))
                    ->withScore(floatval($row[2]))
                    ->withYMin(intval($row[3]))
                    ->withXMin(intval($row[4]))
                    ->withYMax(intval($row[5]))
                    ->withXMax(intval($row[6]))
                    ->build()
            );
        }

        fclose($hotspotCsv);
    }

    private function parseGpsCsv(HotspotCsv $gpsCsv, HotspotAnalysisRecordBuilder $record): void
    {
        $gpsCsv = $gpsCsv->getStream();

        $header = fgetcsv($gpsCsv);
        if ($header === false) {
            throw new RuntimeException(
                'Hotspot csv header is missing'
            );
        }

        $row = fgetcsv($gpsCsv);

        $record->withGpsCoordinates(
            $row ?
                GpsCoordinatesBuilder::gpsCoordinates()
                    ->withLatitudeDegrees(intval($row[0]))
                    ->withLatitudeMinutes(intval($row[1]))
                    ->withLatitudeSeconds(floatval($row[2]))
                    ->withLatitudeDirection($row[3])
                    ->withLongitudeDegrees(intval($row[4]))
                    ->withLongitudeMinutes(intval($row[5]))
                    ->withLongitudeSeconds(floatval($row[6]))
                    ->withLongitudeDirection($row[7])
                    ->build()
                : null
        );
    }
}
