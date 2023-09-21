<?php
declare(strict_types=1);

namespace Hotspot\HotspotResults\Domain\HotspotImage;

use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\AnalysisId;
use Hotspot\HotspotDataset\Domain\Image\ImageId;
use Shared\Domain\Uuid;

interface HotspotImage
{
    public function getId(): Uuid;

    public function getAnalysisId(): AnalysisId;

    public function getImageId(): ImageId;

    public function getName(): string;

    public function getFormat(): string;

    public function getSize(): int;

    public function getContent(): string;

    /** @return resource */
    public function getStream(): mixed;

    public function equals(HotspotImage $other): bool;
}
