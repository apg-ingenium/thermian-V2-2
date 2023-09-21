<?php
declare(strict_types=1);

namespace Authentication\Domain\User;

use RuntimeException;
use Throwable;

class InvalidUserNameException extends RuntimeException
{
    public static function empty(): self
    {
        $message = "The user's name must not be empty";

        return new self($message);
    }

    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
