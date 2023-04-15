<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\Support\Types;

use InvalidArgumentException;

use function sprintf;

final class InvalidType extends InvalidArgumentException
{
    public function __construct(string $collection, string $expected, string $given)
    {
        parent::__construct(
            sprintf('The collection <%s> requires type <%s>, but <%s> was given', $collection, $expected, $given)
        );
    }
}
