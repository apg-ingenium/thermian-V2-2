<?php
declare(strict_types=1);

namespace Hotspot\HotspotResults\UseCase\ZipAnalysisResults;

use RuntimeException;
use Throwable;

class AnalysisFilesNotFoundException extends RuntimeException
{
    public static function create(): self
    {
        return new AnalysisFilesNotFoundException('No analysis files were found');
    }

    public static function withId(string $id): self
    {
        return new AnalysisFilesNotFoundException("No files were found for the analysis with id {$id}");
    }

    private function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
