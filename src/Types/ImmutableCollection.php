<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\Support\Types;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

use function array_flip;
use function array_intersect_key;
use function array_reverse;
use function array_values;
use function count;
use function Lambdish\Phunctional\each;
use function Lambdish\Phunctional\filter;
use function Lambdish\Phunctional\reduce;
use function Lambdish\Phunctional\some;

/**
 * @template TKey of array-key
 * @template TValue
 */
class ImmutableCollection implements Countable, IteratorAggregate
{
    /**
     * @param array<TKey, TValue> $items
     */
    public function __construct(protected iterable $items)
    {
    }

    /**
     * Create new instance.
     *
     * @param array<TKey, TValue> $items
     * @return self<TKey, TValue>
     */
    public static function from(iterable $items): static
    {
        return new static($items);
    }

    /**
     * Create new empty instance.
     *
     * @return self<TKey, TValue>
     */
    public static function empty(): static
    {
        return new static([]);
    }

    /**
     * Reverse items order.
     *
     * @return static<TKey, TValue>
     */
    public function reverse(): static
    {
        return new static(array_reverse($this->items, true));
    }

    /**
     * Get a subset of the items from the given array.
     *
     * @param array $keys
     * @return static<TKey, TValue>
     */
    public function only(array $keys): static
    {
        return new static(array_intersect_key($this->items(), array_flip($keys)));
    }

    /**
     * Get the first item from the collection passing the given truth test.
     *
     * @param callable(TValue, TKey): void $callback
     * @return TValue|null
     */
    public function find(callable $callback): mixed
    {
        foreach ($this->items() as $key => $value) {
            if ($callback($value, $key)) {
                return $value;
            }
        }

        return null;
    }

    /**
     * Execute a callback over each item.
     *
     * @param callable(TValue, TKey): void $callback
     * @return static
     */
    public function each(callable $callback): static
    {
        each($callback, $this->items());
        return $this;
    }

    /**
     * Run a filter over each of the items.
     *
     * @param callable(TValue, TKey): bool $callback
     * @return static
     */
    public function filter(callable $callback): static
    {
        return new static(filter($callback, $this->items()));
    }

    /**
     * Reduce the collection to a single value.
     *
     * @template TReduceInitial
     * @template TReduceReturnType
     *
     * @param callable(TReduceInitial|TReduceReturnType, TValue, TKey): TReduceReturnType $callback
     * @param TReduceInitial $initial
     * @return TReduceReturnType
     */
    public function reduce(callable $callback, mixed $initial = null): mixed
    {
        return reduce($callback, $this->items(), $initial);
    }

    /**
     * Determine if an item exists in the collection.
     *
     * @param callable(TValue, TKey): bool $callback
     * @return bool
     */
    public function some(callable $callback): bool
    {
        return some($callback, $this->items());
    }

    /**
     * Determine if all items pass the given truth test.
     *
     * @param (callable(TValue, TKey): bool) $callback
     * @return bool
     */
    public function every(callable $callback): bool
    {
        foreach ($this->items() as $key => $item) {
            if (!$callback($item, $key)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns the first key that matches the criteria.
     *
     * @param callable(TValue, TKey): bool $criteria
     * @return int|string|null
     */
    public function keyOf(callable $criteria): int|string|null
    {
        foreach ($this->items() as $key => $item) {
            if ($criteria($item, $key)) {
                return $key;
            }
        }

        return null;
    }

    /**
     * Get all the items in the collection.
     *
     * @return array<TKey, TValue>
     */
    public function items(): iterable
    {
        return $this->items;
    }

    /**
     * Returns the values of the array.
     *
     * @return array<int, TValue>
     */
    public function values(): array
    {
        return array_values($this->items());
    }

    /**
     * Count the number of items in the collection.
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * Determine if the list is empty or not.
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    /**
     * Get an iterator for the items.
     *
     * @return ArrayIterator<TKey, TValue>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items());
    }
}
