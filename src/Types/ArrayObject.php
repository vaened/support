<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\Support\Types;

use function Lambdish\Phunctional\any;
use function Lambdish\Phunctional\flatten;
use function Lambdish\Phunctional\map;

abstract class ArrayObject extends ImmutableCollection
{
    public function __construct(array $items)
    {
        $this->ensureType($items);
        parent::__construct($items);
    }

    abstract protected function type(): string;

    public function merge(self $collection): static
    {
        $items = $collection->values();
        $this->ensureType($items);

        return new static([
            ...$this->values(),
            ...$items,
        ]);
    }

    public function flatMap(callable $callback): array
    {
        return flatten($this->map($callback));
    }

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
