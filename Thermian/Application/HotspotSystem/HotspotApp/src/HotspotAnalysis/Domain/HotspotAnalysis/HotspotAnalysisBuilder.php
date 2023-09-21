<?php
declare(strict_types=1);

namespace Hotspot\HotspotAnalysis\Domain\HotspotAnalysis;

use InvalidArgumentException;

class HotspotAnalysisBuilder
{
    private ?AnalysisId $analysisId;
    private ?string $target;

    /** @var array<\Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\HotspotAnalysisRecord> */
    private array $records;

    public function __construct()
    {
        $this->analysisId = null;
        $this->target = null;
        $this->records = [];
    }

    public static function hotspotAnalysis(): self
    {
        return new HotspotAnalysisBuilder();
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

    public function withRecord(HotspotAnalysisRecord $analysisRecord): self
    {
        $this->records[$analysisRecord->getImageId()->value()] = $analysisRecord;

        return $this;
    }

    /** @param array<\Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\HotspotAnalysisRecord> $records*/
    public function withRecords(array $records): self
    {
        $this->records = $records;

        return $this;
    }

    public function build(): HotspotAnalysis
    {
        if (is_null($this->analysisId)) {
            throw new InvalidArgumentException('analysis id is missing');
        }

        if (is_null($this->target)) {
            throw new InvalidArgumentException('analysis target id is missing');
        }

        return HotspotAnalysis::create($this->analysisId, $this->target, $this->records);
    }
}
