<?php
declare(strict_types=1);

namespace Web\Controller\Components;

class RequestValidationVerdict
{
    public static function success(int $code = 200): self
    {
        return new RequestValidationVerdict(true, '', '', $code);
    }

    public static function error(string $message, string $type = '', int $code = 400): self
    {
        return new RequestValidationVerdict(false, $message, $type, $code);
    }

    private bool $isSuccess;
    private string $message;
    private string $type;
    private int $code;

    private function __construct(bool $isSuccess, string $message = '', string $type = '', int $code = 400)
    {
        $this->isSuccess = $isSuccess;
        $this->message = $message;
        $this->type = $type;
        $this->code = $code;
    }

    public function isSuccess(): bool
    {
        return $this->isSuccess;
    }

    public function isError(): bool
    {
        return !$this->isSuccess;
    }

    public function getErrorMessage(): string
    {
        return $this->message;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getCode(): int
    {
        return $this->code;
    }
}
