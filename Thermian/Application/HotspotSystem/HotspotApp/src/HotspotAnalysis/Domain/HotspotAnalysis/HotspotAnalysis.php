<?php
declare(strict_types=1);

namespace Hotspot\HotspotAnalysis\Domain\HotspotAnalysis;

use DateTime;
use Hotspot\HotspotDataset\Domain\Image\ImageId;

class HotspotAnalysis
{
    private AnalysisId $analysisId;
    private DateTime $creationDate;
    private string $target;
    private ?int $numPanels;

    private ?int $numHotspots;
    /** @var array<\Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\HotspotAnalysisRecord> */
    private array $records;

    /** @param array<\Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\HotspotAnalysisRecord> $records */
    public static function create(AnalysisId $analysisId, string $target, array $records, ?DateTime $creationDate = null): HotspotAnalysis
    {
        return new HotspotAnalysis($analysisId, $target, $records, $creationDate);
    }

    /**
     * @param array<\Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\HotspotAnalysisRecord> $records
     */
    private function __construct(AnalysisId $analysisId, string $target, array $records, ?DateTime $creationDate = null)
    {
        $this->creationDate = $creationDate ?? new DateTime();
        $this->analysisId = $analysisId;
        $this->target = $target;
        $this->records = $records;
        $this->numPanels = null;
        $this->numHotspots = null;
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

    /** @return array<\Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\HotspotAnalysisRecord> */
    public function getRecords(): array
    {
        return $this->records;
    }

    public function getNumRecords(): int
    {
        return count($this->records);
    }

    public function getNumPanels(): int
    {
        if (is_null($this->numPanels)) {
            $this->numPanels = array_reduce(
                $this->getRecords(),
                fn(int $value, HotspotAnalysisRecord $record) => $value + $record->getNumPanels(),
                0
            );
        }

        return $this->numPanels;
    }

    public function getNumHotspots(): int
    {
        if (is_null($this->numHotspots)) {
            $this->numHotspots = array_reduce(
                $this->records,
                fn(int $value, HotspotAnalysisRecord $record) => $value + $record->getNumHotspots(),
                0
            );
        }

        return $this->numHotspots;
    }

    public function containsRecordWithId(ImageId $recordId): bool
    {
        return in_array($recordId, array_map(fn($x) => $x->getImageId(), $this->getRecords()));
    }
}
