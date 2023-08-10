<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\Support\Types;

use Vaened\Support\Concerns\ValueStringify;

use function Lambdish\Phunctional\any;

abstract class TypedList extends AbstractList
{
    use ValueStringify;

    public function __construct(iterable $items)
    {
        $this->ensureType($items);
        parent::__construct($items);
    }

    abstract protected function type(): string;

    public function merge(AbstractList $list): static
    {
        $this->ensureType($list->items());
        return parent::merge($list);
    }

    public function overlay(AbstractList $list): static
    {
        $this->ensureType($list->items());
        return parent::overlay($list);
    }

    protected function ensureType(iterable $items): void
    {
        $type = $this->type();

        any(
            in_array($type, $this->natives())
                ? self::ensureThat(static fn(mixed $item) => gettype($item) === $type)
                : self::ensureThat(static fn(mixed $item) => $item instanceof $type),
            $items
        );
    }

    protected function natives(): array
    {
        return ['boolean', 'integer', 'double', 'string', 'array', 'object', 'resource'];
    }

    protected function ensureThat(callable $callback): callable
    {
        $type = $this->type();
        return fn(mixed $item) => $callback($item) ?:
            throw new InvalidType(static::class, $type, $this->valueToString($item));
    }
}
