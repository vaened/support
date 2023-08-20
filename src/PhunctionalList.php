<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\Support;

final class PhunctionalList
{
    public static function same(mixed $target): callable
    {
        return static fn(mixed $item): bool => $item === $target;
    }

    public static function equals(mixed $target): callable
    {
        return static fn(mixed $item): bool => $item == $target;
    }

    public static function different(mixed $target): callable
    {
        return static fn(mixed $item): bool => $item !== $target;
    }

    public static function in(array $options): callable
    {
        return static fn(mixed $item): bool => in_array($item, $options, strict: true);
    }

    public static function null(): callable
    {
        return static fn(mixed $item): bool => $item === null;
    }

    public static function notNull(): callable
    {
        return static fn(mixed $item): bool => $item !== null;
    }

    public static function instanceOf(string $className): callable
    {
        return static fn(mixed $item): bool => $item instanceof $className;
    }

    public static function notInstanceOf(string $className): callable
    {
        return static fn(mixed $item): bool => !$item instanceof $className;
    }
}
