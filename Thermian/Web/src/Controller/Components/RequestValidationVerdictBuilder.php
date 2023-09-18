<?php
declare(strict_types=1);

namespace Web\Controller\Components;

use InvalidArgumentException;

class RequestValidationVerdictBuilder
{
    public static function success(): RequestValidationVerdict
    {
        return RequestValidationVerdict::success();
    }

    public static function serverError(): RequestValidationVerdict
    {
        return RequestValidationVerdictBuilder::error()
            ->message('An unexpected error occurred')
            ->type('server-error')
            ->code(500)
            ->build();
    }

    public static function error(): self
    {
        return new RequestValidationVerdictBuilder();
    }

    private ?int $code;
    private ?string $type;
    private ?string $message;

    private function __construct()
    {
        $this->code = null;
        $this->type = null;
        $this->message = null;
    }

    public function code(int $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function type(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function message(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function build(): RequestValidationVerdict
    {
        if (is_null($this->code)) {
            throw new InvalidArgumentException('status code is missing');
        }

        if (is_null($this->type)) {
            throw new InvalidArgumentException('error type is missing');
        }

        if (is_null($this->message)) {
            throw new InvalidArgumentException('error message is missing');
        }

        return RequestValidationVerdict::error(
            $this->message,
            $this->type,
            $this->code
        );
    }
}
