<?php
declare(strict_types=1);

namespace Hotspot\Test\HotspotAnalysis\Domain\HotspotAnalysis;

use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\AnalysisId;
use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\HotspotAnalysis;
use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\HotspotAnalysisBuilder;
use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\HotspotAnalysisRecord;
use Hotspot\Test\HotspotAnalysis\Domain\Hotspot\TestHotspotBuilder;
use Hotspot\Test\HotspotAnalysis\Domain\Panel\TestPanelBuilder;
use RuntimeException;
use Shared\Domain\Uuid;

class TestHotspotAnalysisBuilder
{
    private ?AnalysisId $analysisId;
    private ?string $target;
    private int $numRecords;
    private int $numPanels;
    private int $numHotspots;

    /** @var array<HotspotAnalysisRecord> */
    private array $records;

    private function __construct()
    {
        $this->analysisId = null;
        $this->target = null;
        $this->records = [];
        $this->numRecords = 0;
        $this->numPanels = 0;
        $this->numHotspots = 0;
    }

    public static function createHotspotAnalysis(): self
    {
        $random = Uuid::random()->value();
        $numRecords = rand(1, 10);
        $numPanels = $numRecords * rand(0, 5);
        $numHotspots = $numPanels * rand(0, 10);

        return (new TestHotspotAnalysisBuilder())
            ->withAnalysisId(AnalysisId::random())
            ->withTarget("Dataset {$random}")
            ->withNumRecords($numRecords)
            ->withNumPanels($numPanels)
            ->withNumHotspots($numHotspots);
    }

    public function withAnalysisId(AnalysisId $analysisId): self
    {
        $this->analysisId = $analysisId;

        return $this;
    }

    public function withTarget(string $target): self
    {
        $this->target = $target;

        return $this;
    }

    public function withRecord(HotspotAnalysisRecord $record): self
    {
        $this->records[] = $record;

        return $this;
    }

    public function withNumRecords(int $numRecords): self
    {
        $this->numRecords = $numRecords;
        if ($numRecords === 0) {
            $this->withNumPanels(0);
        }

        return $this;
    }

    public function withNumPanels(int $numPanels): self
    {
        $this->numPanels = $numPanels;
        if ($numPanels === 0) {
            $this->withNumHotspots(0);
        }

        return $this;
    }

    public function withNumHotspots(int $numHotspots): self
    {
        $this->numHotspots = $numHotspots;

        return $this;
    }

    public function build(): HotspotAnalysis
    {
        if (is_null($this->analysisId)) {
            throw new RuntimeException('The analysis id is missing');
        }

        if (is_null($this->target)) {
            throw new RuntimeException('The target is missing');
        }

        $panelRecord = [];
        for ($index = 0; $index < $this->numPanels; $index++) {
            $panelRecord[$index] = rand(0, max($this->numRecords - 1, 0));
        }

        $hotspotPanel = [];
        for ($index = 0; $index < $this->numHotspots; $index++) {
            $hotspotPanel[$index] = rand(0, max($this->numPanels - 1, 0));
        }

        $graph = array_fill(0, $this->numRecords, []);

        foreach ($panelRecord as $panel => $record) {
            $graph[$record][$panel] = [];
        }

        foreach ($hotspotPanel as $hotspot => $panel) {
            $record = $panelRecord[$panel];
            $graph[$record][$panel][] = $hotspot;
        }

        $records = [];
        foreach ($graph as $recordPanels) {
            $record = TestHotspotAnalysisRecordBuilder
                ::hotspotAnalysisRecord()
                ->withAnalysisId($this->analysisId);

            $panelIndex = 1;
            foreach ($recordPanels as $panelHotspots) {
                $record->withPanel(
                    TestPanelBuilder::random()
                        ->withIndex($panelIndex)
                        ->build()
                );

                $hotspotIndex = 1;
                foreach ($panelHotspots as $hotspot) {
                    $record->withHotspot(
                        TestHotspotBuilder::random()
                            ->withPanelIndex($panelIndex)
                            ->withIndex($hotspotIndex++)
                            ->build()
                    );
                }

                $panelIndex++;
            }

            $records[] = $record->build();
        }

        foreach ($this->records as $record) {
            $records[] = $record;
        }

        return HotspotAnalysisBuilder
            ::hotspotAnalysis()
            ->withAnalysisId($this->analysisId)
            ->withTarget($this->target)
            ->withRecords($records)
            ->build();
    }
}
