<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\Support\Tests\Types;

use Vaened\Support\Tests\TestCase;
use Vaened\Support\Types\ImmutableCollection;

abstract class CollectionTestCase extends TestCase
{
    abstract protected function collection(): ImmutableCollection;

    public function test_count_collection(): void
    {
        $this->assertEquals(3, $this->collection()->count());
    }

    public function test_empty_collection(): void
    {
        $this->assertFalse($this->collection()->isEmpty());
    }
}
