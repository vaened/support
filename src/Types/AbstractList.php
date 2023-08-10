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
use function iterator_to_array;
use function Lambdish\Phunctional\each;
use function Lambdish\Phunctional\filter;
use function Lambdish\Phunctional\flatten;
use function Lambdish\Phunctional\map;
use function Lambdish\Phunctional\reduce;
use function Lambdish\Phunctional\some;

class AbstractList implements Countable, IteratorAggregate
{
    protected array $items;

    public function __construct(iterable $items)
    {
        $this->items = match (true) {
            $items instanceof self => $items->items(),
            $items instanceof Traversable => iterator_to_array($items),
            $items instanceof ArrayIterator => $items->getArrayCopy(),
            default => $items,
        };
    }

    public function reverse(): static
    {
        return new static(array_reverse($this->items, true));
    }

    public function flatMap(callable $callback): iterable
    {
        return flatten($this->map($callback));
    }

    public function map(callable $callback): iterable
    {
        return map($callback, $this->items());
    }

    public function only(array $keys): static
    {
        return new static(array_intersect_key($this->items(), array_flip($keys)));
    }

    public function find(callable $callback): mixed
    {
        foreach ($this->items() as $key => $value) {
            if ($callback($value, $key)) {
                return $value;
            }
        }

        return null;
    }

    public function each(callable $callback): static
    {
        each($callback, $this->items());
        return $this;
    }

    public function filter(callable $callback): static
    {
        return new static(filter($callback, $this->items()));
    }

    public function reduce(callable $callback, mixed $initial = null): mixed
    {
        return reduce($callback, $this->items(), $initial);
    }

    public function some(callable $callback): bool
    {
        return some($callback, $this->items());
    }

    public function every(callable $callback): bool
    {
        foreach ($this->items() as $key => $item) {
            if (!$callback($item, $key)) {
                return false;
            }
        }

        return true;
    }

    public function keyOf(callable $criteria): int|string|null
    {
        foreach ($this->items() as $key => $item) {
            if ($criteria($item, $key)) {
                return $key;
            }
        }

        return null;
    }

    public function items(): array
    {
        return $this->items;
    }

    public function values(): array
    {
        return array_values($this->items());
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items());
    }
}
