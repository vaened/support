<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\Support\Types;

use Vaened\Support\Concerns\ValueStringify;

use function Lambdish\Phunctional\{any};

abstract class SecureList extends AbstractList
{
    use ValueStringify;

    public function __construct(iterable $items)
    {
        $this->ensureTypesOf($items);
        parent::__construct($items);
    }

    abstract static public function type(): string;

    public function merge(AbstractList $list): static
    {
        static::ensureTypesOf($list->items());
        return parent::merge($list);
    }

    public function overlay(AbstractList $list): static
    {
        static::ensureTypesOf($list->items());
        return parent::overlay($list);
    }

    protected function ensureTypesOf(iterable $items): void
    {
        $type = static::type();

        any(
            in_array($type, static::natives())
                ? $this->ensureEachThat(static fn(mixed $item) => gettype($item) === $type)
                : $this->ensureEachThat(static fn(mixed $item) => $item instanceof $type),
            $items
        );
    }

    protected function ensureEachThat(callable $predicate): callable
    {
        $type = static::type();
        return fn(mixed $item) => $predicate($item) ?:
            throw new InvalidSafelistItem(static::class, $type, $this->valueToString($item));
    }

    protected static function natives(): array
    {
        return ['boolean', 'integer', 'double', 'string', 'array', 'object', 'resource'];
    }
}
