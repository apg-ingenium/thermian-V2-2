<?php
declare(strict_types=1);

namespace Hotspot\HotspotResults\Persistence\HotspotCsvRepository;

use Generator;
use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\AnalysisId;
use Hotspot\HotspotDataset\Domain\Image\ImageId;
use Hotspot\HotspotResults\Domain\HotspotCsv\HotspotCsv;
use Hotspot\HotspotResults\Domain\HotspotCsv\HotspotCsvBuilder;
use Hotspot\HotspotResults\Domain\HotspotCsvRepository\HotspotCsvRepository;
use Shared\Domain\Uuid;
use Shared\Persistence\DuplicateIdException;

class FileSystemHotspotCsvRepository implements HotspotCsvRepository
{
    private string $directory;

    public function __construct(string $directory = '/app/persistence/analysis')
    {
        if (!is_dir($directory)) {
            mkdir($directory, 0700, true);
        }

        $this->directory = $directory;
    }

    public function save(HotspotCsv $hotspotCsv): void
    {
        $pathToCsv = $this->pathToCsv(
            $hotspotCsv->getAnalysisId(),
            $hotspotCsv->getImageId(),
            $hotspotCsv->getId()
        );

        if ($this->containsId($hotspotCsv->getId())) {
            throw new DuplicateIdException(
                "duplicate csv id {$hotspotCsv->getId()->value()}"
            );
        }

        mkdir($pathToCsv, 0700, true);
        file_put_contents("{$pathToCsv}/{$hotspotCsv->getName()}", $hotspotCsv->getContent());
    }

    public function containsId(Uuid $id): bool
    {
        return !is_null($this->findPathToCsv($id));
    }

    public function findById(Uuid $id): ?HotspotCsv
    {
        $pathToCsv = $this->findPathToCsv($id);

        if (is_null($pathToCsv)) {
            return null;
        }

        $params = $this->extractParamsFromPath($pathToCsv);

        return HotspotCsvBuilder::hotspotCsv()
            ->withAnalysisId(AnalysisId::fromString($params['analysis_id']))
            ->withImageId(ImageId::fromString($params['image_id']))
            ->withId(Uuid::fromString($params['id']))
            ->withName($params['name'])
            ->fromPath($pathToCsv)
            ->build();
    }

    public function removeByAnalysisId(AnalysisId $analysisId): void
    {
        $pathToAnalysis = $this->pathToAnalysis($analysisId);
        system("rm -rf ${pathToAnalysis}");
    }

    public function findByRecordIdAndName(AnalysisId $analysisId, ImageId $imageId, string $name): ?HotspotCsv
    {
        $pathToCsv = $this->findPathByName($analysisId->value(), $imageId->value(), $name);

        if (!$pathToCsv) {
            return null;
        }

        $params = $this->extractParamsFromPath($pathToCsv);

        return HotspotCsvBuilder::hotspotCsv()
            ->withAnalysisId(AnalysisId::fromString($params['analysis_id']))
            ->withImageId(ImageId::fromString($params['image_id']))
            ->withId(Uuid::fromString($params['id']))
            ->withName($params['name'])
            ->fromPath($pathToCsv)
            ->build();
    }

    public function containsRecordId(AnalysisId $analysisId, ImageId $imageId): bool
    {
        return is_dir($this->pathToRecord($analysisId, $imageId));
    }

    /** @return array<\Hotspot\HotspotResults\Domain\HotspotCsv\HotspotCsv> */
    public function findByRecordId(AnalysisId $analysisId, ImageId $imageId): array
    {
        $pathsToCsvs = glob("{$this->pathToRecord($analysisId, $imageId)}/*/*");
        assert(is_array($pathsToCsvs));

        $recordCsvs = [];
        foreach ($pathsToCsvs as $path) {
            $params = $this->extractParamsFromPath($path);
            $recordCsvs[$params['id']] = HotspotCsvBuilder::hotspotCsv()
                ->withAnalysisId(AnalysisId::fromString($params['analysis_id']))
                ->withImageId(ImageId::fromString($params['image_id']))
                ->withId(Uuid::fromString($params['id']))
                ->withName($params['name'])
                ->fromPath($path)
                ->build();
        }

        return $recordCsvs;
    }

    public function removeByRecordId(AnalysisId $analysisId, ImageId $imageId): void
    {
        $pathToRecord = $this->pathToRecord($analysisId, $imageId);

        if (!is_dir($pathToRecord)) {
            return;
        }

        system("rm -rf {$pathToRecord}");

        $pathToAnalysis = dirname($pathToRecord);
        if ($this->isEmpty($pathToAnalysis)) {
            rmdir($pathToAnalysis);
        }
    }

    private function isEmpty(string $directory): bool
    {
        $dir = opendir($directory);
        assert($dir !== false);

        while (($content = readdir($dir)) !== false) {
            if ($content !== '.' && $content !== '..') {
                closedir($dir);

                return false;
            }
        }
        closedir($dir);

        return true;
    }

    public function removeAll(): void
    {
        system("rm -rf {$this->directory}/*");
    }

    /** @return \Generator<string> */
    private function iterateOverContent(string $directory): Generator
    {
        $contents = scandir($directory);
        assert($contents !== false);
        yield from array_diff($contents, ['.', '..']);
    }

    /** @return \Generator<string> */
    private function iterateOverAnalysisIds(): Generator
    {
        yield from $this->iterateOverContent($this->directory);
    }

    /** @return \Generator<string> */
    private function iterateOverImageIds(string $analysisId): Generator
    {
        $analysisDirectory = "{$this->directory}/{$analysisId}";
        yield from $this->iterateOverContent($analysisDirectory);
    }

    private function pathToAnalysis(AnalysisId $analysisId): string
    {
        return "{$this->directory}/{$analysisId->value()}";
    }

    private function pathToRecord(AnalysisId $analysisId, ImageId $imageId): string
    {
        return "{$this->pathToAnalysis($analysisId)}/{$imageId->value()}";
    }

    private function pathToCsv(AnalysisId $analysisId, ImageId $imageId, Uuid $id): string
    {
        return "{$this->pathToRecord($analysisId, $imageId)}/{$id->value()}";
    }

    private function findPathToCsv(Uuid $id): ?string
    {
        $paths = glob("{$this->directory}/*/*/{$id->value()}/*");
        assert(is_array($paths));

        return $paths ? $paths[0] : null;
    }

    private function findPathByName(string $analysisId, string $imageId, string $name): ?string
    {
        $path = glob("{$this->directory}/{$analysisId}/{$imageId}/{$name}");

        return $path ? $path[0] : null;
    }

    /** @return array<string> */
    private function extractParamsFromPath(string $pathToCsv): array
    {
        $segments = explode('/', $pathToCsv);

        $numSegments = count($segments);

        return [
            'analysis_id' => $segments[$numSegments - 4],
            'image_id' => $segments[$numSegments - 3],
            'id' => $segments[$numSegments - 2],
            'name' => $segments[$numSegments - 1],
        ];
    }
}
