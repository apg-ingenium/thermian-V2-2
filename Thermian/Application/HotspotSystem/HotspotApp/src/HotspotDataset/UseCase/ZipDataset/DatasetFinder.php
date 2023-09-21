<?php
declare(strict_types=1);

namespace Hotspot\HotspotDataset\UseCase\ZipDataset;

use Generator;
use Hotspot\HotspotDataset\Domain\Dataset\DatasetId;

interface DatasetFinder
{
    /** @return \Generator<\Hotspot\HotspotDataset\Domain\File\File> */
    public function findDatasetById(DatasetId $datasetId): Generator;
}
