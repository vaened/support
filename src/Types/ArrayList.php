<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\Support\Types;

use function array_unshift;
use function Lambdish\Phunctional\flat_map;
use function Lambdish\Phunctional\map;

/**
 * @template TKey of array-key
 * @template TValue
 */
class ArrayList extends ImmutableCollection
{
    /**
     * Map a collection and flatten the result by a single level.
     *
     * @param callable(TValue, TKey): mixed $callback
     * @return static<int, mixed>
     */
    public function flatMap(callable $callback): static
    {
        return new static(flat_map($callback, $this->items()));
    }

    /**
     * Run a map over each of the items.
     *
     * @template TMapValue
     *
     * @param callable(TValue, TKey): TMapValue $callback
     * @return static<TKey, TMapValue>
     */
    public function map(callable $callback): static
    {
        return new static(map($callback, $this->items()));
    }

    /**
     * Push an item onto the beginning of the collection.
     *
     * @param TValue $item
     * @param TKey $key
     * @return $this
     */
    public function prepend(mixed $item, string|int $key = null): static
    {
        if (null === $key) {
            array_unshift($this->items, $item);
        } else {
            $this->items = [$key => $item] + $this->items;
        }

        return $this;
    }

    /**
     * Push one item onto the end of the collection.
     *
     * @param TValue $item
     * @return $this
     */
    public function push(mixed $item, string|int $key = null): static
    {
        if (null === $key) {
            $this->items[] = $item;
        } else {
            $this->items[$key] = $item;
        }

        return $this;
    }
}
