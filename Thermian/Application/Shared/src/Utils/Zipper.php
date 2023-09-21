<?php
declare(strict_types=1);

namespace Shared\Utils;

use Generator;
use ZipArchive;

class Zipper
{
    /** @param iterable<string, string> $contents */
    public function zip(string $pathToOutputZip, iterable $contents): void
    {
        $flag = ZipArchive::CREATE;
        if (is_file($pathToOutputZip)) {
            $flag = ZipArchive::OVERWRITE;
        }

        $outputDir = dirname($pathToOutputZip);
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0700, true);
        }

        $zip = new ZipArchive();
        $zip->open($pathToOutputZip, $flag);

        $numFilesInBatch = 0;
        foreach ($contents as $path => $content) {
            $uniquePath = $this->makePathUnique($path, $zip);
            $zip->addFromString($uniquePath, $content);
            $numFilesInBatch++;

            if ($numFilesInBatch === 20) {
                $zip->close();
                $zip = new ZipArchive();
                $zip->open($pathToOutputZip, ZipArchive::CREATE);
                $numFilesInBatch = 0;
            }
        }

        $zip->close();
    }

    private function makePathUnique(string $path, ZipArchive $zip): string
    {
        $index = 1;
        $uniquePath = $path;
        while ($zip->locateName($uniquePath) !== false) {
            $segments = explode('.', $path);

            if (count($segments) === 1) {
                $base = $segments[0];
                $suffix = '';
            } else {
                $suffix = '.' . array_pop($segments);
                $base = implode('.', $segments);
            }

            $uniquePath = "{$base}-{$index}{$suffix}";
            $index++;
        }

        return $uniquePath;
    }

    /** @return \Generator<string, string> */
    public function unzip(string $pathToZip): Generator
    {
        $zip = new ZipArchive();
        $zip->open($pathToZip);

        for ($index = 0; $index < $zip->numFiles; $index++) {
            $name = $zip->getNameIndex($index);
            $content = $zip->getFromIndex($index);
            assert($name !== false);
            assert($content !== false);
            yield $name => $content;
        }
    }
}
