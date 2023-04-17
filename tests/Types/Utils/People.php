<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\Support\Tests\Types\Utils;

use Vaened\Support\Types\ArrayObject;

final class People extends ArrayObject
{
    protected function type(): string
    {
        return Person::class;
    }
}
