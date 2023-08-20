<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\Support\Tests;

use PHPUnit\Framework\TestCase as PhpUnitTestCase;
use Vaened\Support\Types\AbstractList;

class TestCase extends PhpUnitTestCase
{
    public function assertList(array $expected, AbstractList $list): void
    {
        $this->assertEquals($expected, $list->items());
    }
}

