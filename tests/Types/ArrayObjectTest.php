<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\Support\Tests\Types;

use stdClass;
use Vaened\Support\Tests\Types\Utils\People;
use Vaened\Support\Tests\Types\Utils\Person;
use Vaened\Support\Types\ArrayObject;
use Vaened\Support\Types\InvalidType;

use function is_numeric;
use function sprintf;

final class ArrayObjectTest extends CollectionTestCase
{
    private readonly Person $josuke;

    private readonly Person $gyro;

    private readonly Person $jotaro;

    public function test_adding_a_disallowed_type_throws_an_exception(): void
    {
        $template = 'The collection <%s> requires type <%s>, but <%s> was given';
        $this->expectException(InvalidType::class);
        $this->expectExceptionMessage(
            sprintf($template, People::class, Person::class, stdClass::class)
        );
        new People([new stdClass()]);
    }

    public function test_reverse_objects(): void
    {
        $items = $this->collection()->reverse()->items();

        $this->assertEquals([
            2 => $this->josuke,
            1 => $this->gyro,
            0 => $this->jotaro,
        ], $items);
    }

    public function test_only_object(): void
    {
        $items = $this->collection()->only([2])->items();

        $this->assertEquals([2 => $this->josuke], $items);
    }

    public function test_find_object_by_value(): void
    {
        $item = $this->collection()->find(static fn(Person $person) => $person->name === 'Gyro');
        $this->assertEquals($this->gyro, $item);
    }

    public function test_find_object_by_key(): void
    {
        $item = $this->collection()->find(static fn(Person $person, int $key) => $key === 0);
        $this->assertEquals($this->jotaro, $item);
    }

    public function test_each_objects(): void
    {
        $items = [];

        $this->collection()->each(function (Person $person, int $key) use (&$items) {
            $items[$key] = $person->name;
        });

        $this->assertEquals([0 => 'Jotaro', 1 => 'Gyro', 2 => 'Josuke'], $items);
    }

    public function test_filter_object_by_value(): void
    {
        $items = $this->collection()->filter(static fn(Person $person, int $key) => $person->name === 'Josuke')->items();

        $this->assertEquals([2 => $this->josuke], $items);
    }

    public function test_filter_object_by_key(): void
    {
        $items = $this->collection()->filter(static fn(Person $person, int $key) => $key === 1)->items();

        $this->assertEquals([1 => $this->gyro], $items);
    }

    public function test_reduce_objects(): void
    {
        $sum = $this->collection()->reduce(function (array &$acc, Person $person, int $key) {
            $acc[] = sprintf('[%d][%s]', $key, $person->name);
            return $acc;
        }, []);

        $this->assertEquals([
            '[0][Jotaro]',
            '[1][Gyro]',
            '[2][Josuke]',
        ], $sum);
    }

    public function test_some_object_by_value(): void
    {
        $this->assertTrue($this->collection()->some(static fn(Person $person) => $person->name === 'Gyro'));
        $this->assertFalse($this->collection()->some(static fn(Person $person) => $person->name === 'Non'));
    }

    public function test_some_object_by_key(): void
    {
        $this->assertTrue($this->collection()->some(static fn(Person $person, int $key) => $key === 1));
        $this->assertFalse($this->collection()->some(static fn(Person $person, int $key) => $key === 4));
    }

    public function test_every_object_by_value(): void
    {
        $this->assertTrue($this->collection()->some(static fn(Person $person) => !empty($person->name)));
        $this->assertFalse($this->collection()->some(static fn(Person $person) => empty($person->name)));
    }

    public function test_every_object_by_key(): void
    {
        $this->assertTrue($this->collection()->some(static fn(Person $person, mixed $key) => is_numeric($key)));
        $this->assertFalse($this->collection()->some(static fn(Person $person, mixed $key) => !is_numeric($key)));
    }

    public function test_key_object_of(): void
    {
        $this->assertEquals(1, $this->collection()->keyOf(static fn(Person $person) => $person->name === 'Gyro'));
        $this->assertEquals(0, $this->collection()->keyOf(static fn(Person $person) => $person->name === 'Jotaro'));
    }

    public function test_object_values(): void
    {
        $this->assertEquals([
            $this->jotaro,
            $this->gyro,
            $this->josuke,
        ], $this->collection()->values());
    }

    public function test_merge_two_objects_collection_into_a_new_one(): void
    {
        $dio           = new Person('Dio');
        $newCollection = $this->collection()->merge(new People([$dio]));

        $this->assertEquals([
            $this->jotaro,
            $this->gyro,
            $this->josuke,
            $dio
        ], $newCollection->values());
    }

    public function test_merging_collection_of_objects_of_different_types_throws_an_exception(): void
    {
        $this->expectException(InvalidType::class);
        $this->collection()->merge(new class extends ArrayObject {
            public function __construct()
            {
                parent::__construct([new stdClass()]);
            }

            protected function type(): string
            {
                return stdClass::class;
            }
        });
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->jotaro = Person::create('Jotaro');
        $this->gyro   = Person::create('Gyro');
        $this->josuke = Person::create('Josuke');
    }

    protected function collection(): People
    {
        return new People([
            $this->jotaro,
            $this->gyro,
            $this->josuke,
        ]);
    }
}
