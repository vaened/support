<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\Support\Types;

use function array_unshift;

class ArrayList extends AbstractList
{
    public static function from(iterable $items): static
    {
        return new static($items);
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
