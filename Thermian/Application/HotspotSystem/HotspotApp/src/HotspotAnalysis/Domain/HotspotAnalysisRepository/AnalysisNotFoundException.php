<?php
declare(strict_types=1);

namespace Hotspot\HotspotAnalysis\Domain\HotspotAnalysisRepository;

use RuntimeException;
use Throwable;

class AnalysisNotFoundException extends RuntimeException
{
    public static function create(): self
    {
        return new AnalysisNotFoundException('Analysis not found');
    }

    public static function withId(string $id): self
    {
        return new AnalysisNotFoundException("Analysis not found with id {$id}");
    }

    private function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
