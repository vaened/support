<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\Support\Types;

use function Lambdish\Phunctional\any;

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

    private function ensureType(array $items): void
    {
        $type = $this->type();
        any(
            fn(mixed $item) => $item instanceof $type ?: throw new InvalidType(static::class, $type, $item::class),
            $items
        );
    }
}
