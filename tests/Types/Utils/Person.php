<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\Support\Tests\Types\Utils;

final class Person
{
    public function __construct(public readonly string $name)
    {
    }

    public static function create(string $name): self
    {
        return new self($name);
    }
}
