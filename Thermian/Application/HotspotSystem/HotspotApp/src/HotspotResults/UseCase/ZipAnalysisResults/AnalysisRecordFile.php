<?php
declare(strict_types=1);

namespace Hotspot\HotspotResults\UseCase\ZipAnalysisResults;

class AnalysisRecordFile
{
    private string $recordName;
    private string $fileName;
    private string $fileContent;

    public static function create(string $recordName, string $fileName, string $fileContent): self
    {
        return new AnalysisRecordFile($recordName, $fileName, $fileContent);
    }

    private function __construct(string $recordName, string $fileName, string $fileContent)
    {
        $this->recordName = $recordName;
        $this->fileName = $fileName;
        $this->fileContent = $fileContent;
    }

    public function getRecordName(): string
    {
        return $this->recordName;
    }

    public function getName(): string
    {
        return $this->fileName;
    }

    public function getContent(): string
    {
        return $this->fileContent;
    }
}
