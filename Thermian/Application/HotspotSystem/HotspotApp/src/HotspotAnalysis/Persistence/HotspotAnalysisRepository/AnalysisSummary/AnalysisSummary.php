<?php
declare(strict_types=1);

namespace Hotspot\HotspotAnalysis\Persistence\HotspotAnalysisRepository\AnalysisSummary;

use DateTime;
use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\AnalysisId;

class AnalysisSummary
{
    private AnalysisId $analysisId;
    private string $target;
    private DateTime $creationDate;
    private int $numRecords;
    private int $numPanels;
    private int $numHotspots;

    public static function create(
        AnalysisId $analysisId,
        string $target,
        DateTime $creationDate,
        int $numRecords,
        int $numPanels,
        int $numHotspots
    ): self {
        return new AnalysisSummary($analysisId, $target, $creationDate, $numRecords, $numPanels, $numHotspots);
    }

    public function __construct(
        AnalysisId $analysisId,
        string $target,
        DateTime $creationDate,
        int $numRecords,
        int $numPanels,
        int $numHotspots
    ) {
        $this->analysisId = $analysisId;
        $this->target = $target;
        $this->creationDate = $creationDate;
        $this->numRecords = $numRecords;
        $this->numPanels = $numPanels;
        $this->numHotspots = $numHotspots;
    }

    public function getId(): AnalysisId
    {
        return $this->analysisId;
    }

    public function getTarget(): string
    {
        return $this->target;
    }

    public function getCreationDate(): DateTime
    {
        return $this->creationDate;
    }

    public function getNumRecords(): int
    {
        return $this->numRecords;
    }

    public function getNumPanels(): int
    {
        return $this->numPanels;
    }

    public function getNumHotspots(): int
    {
        return $this->numHotspots;
    }
}
