<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\Support\Types;

use function array_unshift;
use function Lambdish\Phunctional\flat_map;
use function Lambdish\Phunctional\map;

class ArrayList extends ImmutableCollection
{
    public function merge(self $collection): static
    {
        return new static([
            ...$this->values(),
            ...$collection->values(),
        ]);
    }

    public function flatMap(callable $callback): static
    {
        return new static(flat_map($callback, $this->items()));
    }

    public function map(callable $callback): static
    {
        return new static(map($callback, $this->items()));
    }

    public function prepend(mixed $item, string|int $key = null): static
    {
        if (null === $key) {
            array_unshift($this->items, $item);
        } else {
            $this->items = [$key => $item] + $this->items;
        }

        return $this;
    }

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
