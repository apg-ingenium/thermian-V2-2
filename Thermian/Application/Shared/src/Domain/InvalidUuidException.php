<?php
declare(strict_types=1);

namespace Shared\Domain;

use RuntimeException;
use Throwable;

class InvalidUuidException extends RuntimeException
{
    public static function forId(string $value): self
    {
        return new InvalidUuidException("Invalid Uuid $value");
    }

    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
