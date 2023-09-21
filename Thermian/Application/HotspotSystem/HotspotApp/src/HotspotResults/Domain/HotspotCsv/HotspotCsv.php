<?php
declare(strict_types=1);

namespace Hotspot\HotspotResults\Domain\HotspotCsv;

use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\AnalysisId;
use Hotspot\HotspotDataset\Domain\Image\ImageId;
use Shared\Domain\Uuid;

interface HotspotCsv
{
    public function getAnalysisId(): AnalysisId;

    public function getId(): Uuid;

    public function getImageId(): ImageId;

    public function getSize(): int;

    public function getContent(): string;

    /** @return resource */
    public function getStream(): mixed;

    public function equals(HotspotCsv $other): bool;

    public function getName(): string;
}
