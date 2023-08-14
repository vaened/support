<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\Support\Concerns;

use DateTimeInterface;
use Stringable;

use function get_resource_type;
use function is_array;
use function is_bool;
use function is_resource;
use function is_scalar;

trait ValueStringify
{
    protected static function valueToString(mixed $value): string
    {
        if ($value === null) {
            return 'NULL';
        }

        if (is_bool($value)) {
            return $value ? 'TRUE' : 'FALSE';
        }

        if (is_array($value)) {
            return 'Array';
        }

        if (is_scalar($value)) {
            return (string)$value;
        }

        if (is_resource($value)) {
            return '(' . get_resource_type($value) . ' resource #' . (int)$value . ')';
        }

        if ($value instanceof Stringable) {
            return $value->__toString();
        }

        if ($value instanceof DateTimeInterface) {
            return $value->format('c');
        }

        return $value::class;
    }
}
