<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\Support\Types;

use Vaened\Support\Concerns\ValueStringify;

use function array_values;
use function Lambdish\Phunctional\{any, flat_map, map};

abstract class SecureList extends AbstractList
{
    use ValueStringify;

    public function __construct(iterable $items)
    {
        parent::__construct(self::processSecureItems($items));
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
        return new ArrayList(array_values(flat_map($mapper, $this->items())));
    }

    public function map(callable $mapper): ArrayList
    {
        return new ArrayList(array_values(map($mapper, $this->items())));
    }

    protected static function processSecureItems(iterable $items): iterable
    {
        static::ensureType($items);
        return $items;
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

    protected static function ensureThat(callable $predicate): callable
    {
        $type = static::type();
        return fn(mixed $item) => $predicate($item) ?:
            throw new InvalidType(static::class, $type, static::valueToString($item));
    }
}
