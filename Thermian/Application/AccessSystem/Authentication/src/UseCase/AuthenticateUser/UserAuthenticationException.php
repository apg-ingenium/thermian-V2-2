<?php
declare(strict_types=1);

namespace Authentication\UseCase\AuthenticateUser;

use RuntimeException;
use Throwable;

class UserAuthenticationException extends RuntimeException
{
    public static function invalidCredentials(): self
    {
        $message = 'Invalid user credentials';

        return new self($message);
    }

    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
