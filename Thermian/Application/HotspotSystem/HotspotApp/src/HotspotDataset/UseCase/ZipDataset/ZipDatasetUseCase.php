<?php
declare(strict_types=1);

namespace Hotspot\HotspotDataset\UseCase\ZipDataset;

use Generator;
use Hotspot\HotspotDataset\Domain\Dataset\DatasetId;
use Hotspot\HotspotDataset\Domain\DatasetRepository\DatasetNotFoundException;
use Shared\Utils\Zipper;

class ZipDatasetUseCase
{
    private DatasetFinder $datasetFinder;
    private Zipper $zipper;

    public function __construct(DatasetFinder $datasetFinder, Zipper $zipper)
    {
        $this->datasetFinder = $datasetFinder;
        $this->zipper = $zipper;
    }

    public function execute(ZipDatasetCommand $command): void
    {
        $datasetId = DatasetId::fromString($command->getDatasetId());
        $pathToOutputZip = $command->getOutputZipPath();

        $datasetFiles = $this->datasetFinder->findDatasetById($datasetId);

        if (!$datasetFiles->valid()) {
            throw DatasetNotFoundException::withId(
                $command->getDatasetId()
            );
        }

        $structuredDatasetFiles = $this->structure($datasetFiles);
        $this->zipper->zip($pathToOutputZip, $structuredDatasetFiles);
    }

    /**
     * @param \Generator<\Hotspot\HotspotDataset\Domain\File\File> $datasetFiles
     * @return \Generator<string, string>
     */
    private function structure(Generator $datasetFiles): Generator
    {
        foreach ($datasetFiles as $file) {
            yield $file->getName() => $file->getContent();
        }
    }
}
