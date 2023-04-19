<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\Support\Types;

use function Lambdish\Phunctional\any;
use function Lambdish\Phunctional\flatten;
use function Lambdish\Phunctional\map;

/**
 * @template TKey of array-key
 * @template TValue
 * @extends ArrayObject<TKey, TValue>
 */
abstract class ArrayObject extends ImmutableCollection
{
    /**
     * @param array<TKey, TValue> $items
     */
    public function __construct(iterable $items)
    {
        $this->ensureType($items);
        parent::__construct($items);
    }

    /**
     * Map a collection and flatten the result by a single level.
     *
     * @return class-string<TValue>
     */
    abstract protected function type(): string;

    /**
     * Run a map over each of the items.
     *
     * @template TMapValue
     *
     * @param callable(TValue, TKey): TMapValue $callback
     * @return array<TKey, TMapValue>
     */
    public function flatMap(callable $callback): array
    {
        return flatten($this->map($callback));
    }

    /**
     * Run a map over each of the items.
     *
     * @template TMapValue
     *
     * @param callable(TValue, TKey): TMapValue $callback
     * @return array<TKey, TMapValue>
     */
    public function map(callable $callback): array
    {
        return map($callback, $this->items());
    }

    private function ensureType(array $items): void
    {
        $type = $this->type();
        any(
            fn(mixed $item) => $item instanceof $type ?: throw new InvalidType(static::class, $type, $item::class),
            $items
        );
    }
}
