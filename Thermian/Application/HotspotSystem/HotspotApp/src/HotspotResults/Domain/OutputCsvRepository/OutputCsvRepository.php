<?php
declare(strict_types=1);

namespace Hotspot\HotspotResults\Domain\OutputCsvRepository;

use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\AnalysisId;
use Hotspot\HotspotDataset\Domain\Image\ImageId;

interface OutputCsvRepository
{
    /** @return array<\Hotspot\HotspotResults\Domain\HotspotCsv\HotspotCsv> */
    public function findByRecordId(AnalysisId $analysisId, ImageId $imageId): array;

    /** @return array<array<\Hotspot\HotspotResults\Domain\HotspotCsv\HotspotCsv>> */
    public function findByAnalysisId(AnalysisId $analysisId): array;
}
