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

use function array_reverse;
use function array_values;
use function count;
use function iterator_to_array;
use function Lambdish\Phunctional\{each, filter, flat_map, map, reduce, some};

abstract class AbstractList implements Countable, IteratorAggregate
{
    protected array $items;

    public function __construct(iterable $items)
    {
        $this->items = self::parse($items);
    }

    public function merge(self $list): static
    {
        return new static([
            ...$this->values(),
            ...$list->values(),
        ]);
    }

    public function overlay(self $list): static
    {
        $items = $this->items();

        $list->each(function (mixed $item, int|string $key) use (&$items) {
            $items[$key] = $item;
        });

        return new static($items);
    }

    public function reverse(): static
    {
        return new static(array_reverse($this->items, true));
    }

    public function flatMap(callable $mapper): self|static
    {
        return new static(flat_map($mapper, $this->items()));
    }

    public function map(callable $mapper): self|static
    {
        return new static(map($mapper, $this->items()));
    }

    public function pick(callable $predicate): mixed
    {
        foreach ($this->items() as $key => $value) {
            if ($predicate($value, $key)) {
                return $value;
            }
        }

        return null;
    }

    public function each(callable $action): static
    {
        each($action, $this->items());
        return $this;
    }

    public function filter(callable $predicate): static
    {
        return new static(filter($predicate, $this->items()));
    }

    public function reduce(callable $accumulator, mixed $initial = null): mixed
    {
        return reduce($accumulator, $this->items(), $initial);
    }

    public function some(callable $predicate): bool
    {
        return some($predicate, $this->items());
    }

    public function keyOf(callable $predicate): int|string|null
    {
        foreach ($this->items() as $key => $item) {
            if ($predicate($item, $key)) {
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

    private static function parse(iterable $items): array
    {
        return match (true) {
            $items instanceof self => $items->items(),
            $items instanceof Traversable => iterator_to_array($items),
            $items instanceof ArrayIterator => $items->getArrayCopy(),
            default => $items,
        };
    }
}
