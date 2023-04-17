<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\Support\Tests\Types;

use Vaened\Support\Types\ArrayList;
use Vaened\Support\Types\ImmutableCollection;

use function is_numeric;

final class ArrayListTest extends CollectionTestCase
{
    public function test_reverse(): void
    {
        $items = $this->collection()->reverse()->items();

        $this->assertEquals(['c' => 3, 'b' => 2, 'a' => 1], $items);
    }

    public function test_only(): void
    {
        $items = $this->collection()->only(['a', 'c'])->items();

        $this->assertEquals(['a' => 1, 'c' => 3], $items);
    }

    public function test_find_by_value(): void
    {
        $item = $this->collection()->find(static fn(int $value, string $key) => $value === 1);
        $this->assertEquals(1, $item);
    }

    public function test_find_by_key(): void
    {
        $item = $this->collection()->find(static fn(int $value, string $key) => $key === 'c');
        $this->assertEquals(3, $item);
    }

    public function test_each(): void
    {
        $totalItems = [];

        $this->collection()->each(function (int $value, string $key) use (&$totalItems) {
            $totalItems[$key] = $value;
        });

        $this->assertEquals(['a' => 1, 'b' => 2, 'c' => 3], $totalItems);
    }

    public function test_filter_by_value(): void
    {
        $items = $this->collection()->filter(static fn(int $value, string $key) => $value % 2 === 0)->items();

        $this->assertEquals(['b' => 2], $items);
    }

    public function test_filter_by_key(): void
    {
        $items = $this->collection()->filter(static fn(int $value, string $key) => !empty($key))->items();

        $this->assertEquals(['a' => 1, 'b' => 2, 'c' => 3], $items);
    }

    public function test_reduce(): void
    {
        $sum = $this->collection()->reduce(function (int &$acc, int $value) {
            $acc += $value;
            return $acc;
        }, 0);

        $this->assertEquals(6, $sum);
    }

    public function test_some_by_value(): void
    {
        $this->assertTrue($this->collection()->some(static fn(int $value) => $value === 1));
        $this->assertFalse($this->collection()->some(static fn(int $value) => $value === 4));
    }

    public function test_some_by_key(): void
    {
        $this->assertTrue($this->collection()->some(static fn(int $value, string $key) => $key === 'a'));
        $this->assertFalse($this->collection()->some(static fn(int $value, string $key) => $key === 'd'));
    }

    public function test_every_by_value(): void
    {
        $this->assertTrue($this->collection()->some(static fn(mixed $value) => is_numeric($value)));
        $this->assertFalse($this->collection()->some(static fn(mixed $value) => !is_numeric($value)));
    }

    public function test_every_by_key(): void
    {
        $this->assertTrue($this->collection()->some(static fn(int $value, string $key) => !empty($key)));
        $this->assertFalse($this->collection()->some(static fn(int $value, string $key) => empty($key)));
    }

    public function test_key_of(): void
    {
        $this->assertEquals('a', $this->collection()->keyOf(static fn(int $value) => $value === 1));
    }

    public function test_values(): void
    {
        $this->assertEquals([1, 2, 3], $this->collection()->values());
    }

    protected function collection(): ImmutableCollection
    {
        return ArrayList::from([
            'a' => 1,
            'b' => 2,
            'c' => 3
        ]);
    }
}
