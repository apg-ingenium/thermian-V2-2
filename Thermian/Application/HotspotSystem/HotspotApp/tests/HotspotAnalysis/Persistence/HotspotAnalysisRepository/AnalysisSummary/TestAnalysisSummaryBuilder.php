<?php
declare(strict_types=1);

namespace Hotspot\Test\HotspotAnalysis\Persistence\HotspotAnalysisRepository\AnalysisSummary;

use DateTime;
use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\AnalysisId;
use Hotspot\HotspotAnalysis\Persistence\HotspotAnalysisRepository\AnalysisSummary\AnalysisSummary;
use RuntimeException;

class TestAnalysisSummaryBuilder
{
    private ?AnalysisId $analysisId;
    private ?string $analysisTarget;
    private ?DateTime $analysisDate;
    private ?int $numRecords;
    private ?int $numPanels;
    private ?int $numHotspots;

    public static function random(): self
    {
        $randomIndex = mt_rand(0, 1000000);
        $randomTarget = "Dataset ${randomIndex}";

        $randomDay = mt_rand(1, 28);
        $randomMonth = mt_rand(1, 12);
        $randomYear = mt_rand(2000, 3000);
        $randomDate = "{$randomYear}-{$randomMonth}-{$randomDay}";

        return TestAnalysisSummaryBuilder
            ::hotspotAnalysisSummary()
            ->withAnalysisId(AnalysisId::random())
            ->withAnalysisTarget($randomTarget)
            ->createdAt(new DateTime($randomDate))
            ->withNumRecords(mt_rand(0, 100))
            ->withNumPanels(mt_rand(0, 300))
            ->withNumHotspots(mt_rand(0, 5000));
    }

    public static function hotspotAnalysisSummary(): self
    {
        return new TestAnalysisSummaryBuilder();
    }

    public function __construct()
    {
        $this->analysisId = null;
        $this->analysisTarget = null;
        $this->analysisDate = null;
        $this->numRecords = null;
        $this->numPanels = null;
        $this->numHotspots = null;
    }

    public function withAnalysisId(AnalysisId $analysisId): self
    {
        $this->analysisId = $analysisId;

        return $this;
    }

    public function withAnalysisTarget(string $target): self
    {
        $this->analysisTarget = $target;

        return $this;
    }

    public function createdAt(DateTime $date): self
    {
        $this->analysisDate = $date;

        return $this;
    }

    public function withNumRecords(int $numRecords): self
    {
        $this->numRecords = $numRecords;

        return $this;
    }

    public function withNumPanels(int $numPanels): self
    {
        $this->numPanels = $numPanels;

        return $this;
    }

    public function withNumHotspots(int $numHotspots): self
    {
        $this->numHotspots = $numHotspots;

        return $this;
    }

    public function build(): AnalysisSummary
    {
        if (is_null($this->analysisId)) {
            throw new RuntimeException('Analysis id is missing');
        }

        if (is_null($this->analysisTarget)) {
            throw new RuntimeException('Analysis target is missing');
        }

        if (is_null($this->analysisDate)) {
            throw new RuntimeException('Analysis creation date is missing');
        }

        if (is_null($this->numRecords)) {
            throw new RuntimeException('Analysis num records is missing');
        }

        if (is_null($this->numPanels)) {
            throw new RuntimeException('Analysis num panels is missing');
        }

        if (is_null($this->numHotspots)) {
            throw new RuntimeException('Analysis num hotspots is missing');
        }

        return AnalysisSummary::create(
            $this->analysisId,
            $this->analysisTarget,
            $this->analysisDate,
            $this->numRecords,
            $this->numPanels,
            $this->numHotspots
        );
    }
}
