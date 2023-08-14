<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\Support\Types;

use Vaened\Support\Concerns\ValueStringify;

use function Lambdish\Phunctional\{any, flat_map, map};

abstract class SecureList extends AbstractList
{
    use ValueStringify;

    public function __construct(iterable $items)
    {
        static::ensureType($items);
        parent::__construct($items);
    }

    abstract static protected function type(): string;

    public function merge(AbstractList $list): static
    {
        static::ensureType($list->items());
        return parent::merge($list);
    }

    public function overlay(AbstractList $list): static
    {
        static::ensureType($list->items());
        return parent::overlay($list);
    }

    public function flatMap(callable $mapper): ArrayList
    {
        return new ArrayList(flat_map($mapper, $this->items()));
    }

    public function map(callable $mapper): ArrayList
    {
        return new ArrayList(map($mapper, $this->items()));
    }

    protected static function ensureType(iterable $items): void
    {
        $type = static::type();

        any(
            in_array($type, static::natives())
                ? self::ensureThat(static fn(mixed $item) => gettype($item) === $type)
                : self::ensureThat(static fn(mixed $item) => $item instanceof $type),
            $items
        );
    }

    protected static function natives(): array
    {
        return ['boolean', 'integer', 'double', 'string', 'array', 'object', 'resource'];
    }

    protected static function ensureThat(callable $callback): callable
    {
        $type = static::type();
        return fn(mixed $item) => $callback($item) ?:
            throw new InvalidType(static::class, $type, static::valueToString($item));
    }
}
