<?php
declare(strict_types=1);

namespace Shared\Domain;

use Ramsey\Uuid\Uuid as RamseyUuid;

class Uuid
{
    public static function random(): static
    {
        return new static(RamseyUuid::uuid4()->toString());
    }

    public static function fromString(string $value): static
    {
        return new static($value);
    }

    public static function fromBinary(string $value): static
    {
        $uuid = preg_replace(
            '/([0-9a-f]{8})([0-9a-f]{4})([0-9a-f]{4})([0-9a-f]{4})([0-9a-f]{12})/',
            '$1-$2-$3-$4-$5',
            bin2hex($value)
        );

        if (is_null($uuid)) {
            throw InvalidUuidException::forId($value);
        }

        return new static($uuid);
    }

    private string $value;

    final private function __construct(string $value)
    {
        $this->validate($value);
        $this->value = $value;
    }

    private function validate(string $value): void
    {
        if (!RamseyUuid::isValid($value)) {
            throw InvalidUuidException::forId($value);
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function binary(): string
    {
        $binaryUuid = hex2bin(str_replace('-', '', $this->value));
        assert($binaryUuid !== false);

        return $binaryUuid;
    }

    public function equals(Uuid $other): bool
    {
        return $this->value() === $other->value();
    }
}
