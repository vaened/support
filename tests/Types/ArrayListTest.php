<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\Support\Tests\Types;

use PHPUnit\Framework\Attributes\Test;
use Vaened\Support\Types\ArrayList;

use function is_numeric;
use function Lambdish\Phunctional\map;
use function sprintf;

final class ArrayListTest extends ListTestCase
{
    #[Test]
    public function reverse(): void
    {
        $items = $this->collection()->reverse()->items();

        $this->assertEquals(['c' => 3, 'b' => 2, 'a' => 1], $items);
    }

    #[Test]
    public function find_by_value(): void
    {
        $item = $this->collection()->pick(static fn(int $value, string $key) => $value === 1);
        $this->assertEquals(1, $item);
    }

    #[Test]
    public function find_by_key(): void
    {
        $item = $this->collection()->pick(static fn(int $value, string $key) => $key === 'c');
        $this->assertEquals(3, $item);
    }

    #[Test]
    public function each(): void
    {
        $totalItems = [];

        $this->collection()->each(function (int $value, string $key) use (&$totalItems) {
            $totalItems[$key] = $value;
        });

        $this->assertEquals(['a' => 1, 'b' => 2, 'c' => 3], $totalItems);
    }

    #[Test]
    public function filter_by_value(): void
    {
        $items = $this->collection()->filter(static fn(int $value, string $key) => $value % 2 === 0)->items();

        $this->assertEquals(['b' => 2], $items);
    }

    #[Test]
    public function filter_by_key(): void
    {
        $items = $this->collection()->filter(static fn(int $value, string $key) => !empty($key))->items();

        $this->assertEquals(['a' => 1, 'b' => 2, 'c' => 3], $items);
    }

    #[Test]
    public function reduce(): void
    {
        $sum = $this->collection()->reduce(function (int &$acc, int $value) {
            $acc += $value;
            return $acc;
        }, 0);

        $this->assertEquals(6, $sum);
    }

    #[Test]
    public function some_by_value(): void
    {
        $this->assertTrue($this->collection()->some(static fn(int $value) => $value === 1));
        $this->assertFalse($this->collection()->some(static fn(int $value) => $value === 4));
    }

    #[Test]
    public function some_by_key(): void
    {
        $this->assertTrue($this->collection()->some(static fn(int $value, string $key) => $key === 'a'));
        $this->assertFalse($this->collection()->some(static fn(int $value, string $key) => $key === 'd'));
    }

    #[Test]
    public function every_by_value(): void
    {
        $this->assertTrue($this->collection()->some(static fn(mixed $value) => is_numeric($value)));
        $this->assertFalse($this->collection()->some(static fn(mixed $value) => !is_numeric($value)));
    }

    #[Test]
    public function every_by_key(): void
    {
        $this->assertTrue($this->collection()->some(static fn(int $value, string $key) => !empty($key)));
        $this->assertFalse($this->collection()->some(static fn(int $value, string $key) => empty($key)));
    }

    #[Test]
    public function key_of(): void
    {
        $this->assertEquals('a', $this->collection()->keyOf(static fn(int $value) => $value === 1));
    }

    #[Test]
    public function values(): void
    {
        $this->assertEquals([1, 2, 3], $this->collection()->values());
    }

    #[Test]
    public function merge_two_collection_into_a_new_one(): void
    {
        $newCollection = $this->collection()->merge(new ArrayList([4, 5, 6, 7]));

        $this->assertEquals([1, 2, 3, 4, 5, 6, 7], $newCollection->values());
    }

    #[Test]
    public function overlay_two_collection_into_a_new_one(): void
    {
        $newCollection = $this->collection()->overlay(new ArrayList([4, 5, 'c' => 6, 7]));

        $this->assertEquals([
            'a' => 1,
            'b' => 2,
            'c' => 6,
            0   => 4,
            1   => 5,
            2   => 7

        ], $newCollection->items());
    }

    #[Test]
    public function map_to_names(): void
    {
        $names = $this->collection()->map(
            static fn(int $value, string $key) => sprintf('%s:%d', $key, $value)
        );

        $this->assertInstanceOf(ArrayList::class, $names);
        $this->assertEquals([0 => 'a:1', 1 => 'b:2', 2 => 'c:3'], $names->items());
    }

    #[Test]
    public function flat_map_multiplied_by_two(): void
    {
        $numbers = (new ArrayList([[1, 2], [3, 4], [5, 6]]))->flatMap(
            static fn(array $numbers) => map(
                static fn(int $number) => $number * 2,
                $numbers
            ),
        );

        $this->assertEquals([2, 4, 6, 8, 10, 12], $numbers->items());
    }

    #[Test]
    public function filter_duplicates(): void
    {
        $this->assertEquals([4 => 5, 5 => 2], (new ArrayList([1, 2, 4, 5, 5, 2]))->duplicates()->items());
    }

    #[Test]
    public function filter_uniques(): void
    {
        $numbers = new ArrayList([1, 2, 4, 5, 5, 2]);
        $this->assertEquals([1, 2, 4, 5], $numbers->unique()->items());
    }

    protected function collection(): ArrayList
    {
        return new ArrayList([
            'a' => 1,
            'b' => 2,
            'c' => 3
        ]);
    }
}
