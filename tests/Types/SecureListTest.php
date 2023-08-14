<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\Support\Tests\Types;

use PHPUnit\Framework\Attributes\Test;
use stdClass;
use Vaened\Support\Tests\Types\Utils\Person;
use Vaened\Support\Tests\Types\Utils\StronglySecureList;
use Vaened\Support\Types\InvalidType;
use Vaened\Support\Types\SecureList;

use function is_numeric;
use function sprintf;

final class SecureListTest extends ListTestCase
{
    private readonly Person $josuke;

    private readonly Person $gyro;

    private readonly Person $jotaro;

    #[Test]
    public function adding_a_disallowed_type_throws_an_exception(): void
    {
        $template = 'The collection <%s> requires type <%s>, but <%s> was given';
        $this->expectException(InvalidType::class);
        $this->expectExceptionMessage(
            sprintf($template, StronglySecureList::class, Person::class, stdClass::class)
        );
        new StronglySecureList([new stdClass()]);
    }

    #[Test]
    public function reverse_objects(): void
    {
        $items = $this->collection()->reverse()->items();

        $this->assertEquals([
            2 => $this->josuke,
            1 => $this->gyro,
            0 => $this->jotaro,
        ], $items);
    }

    #[Test]
    public function find_object_by_value(): void
    {
        $item = $this->collection()->pick(static fn(Person $person) => $person->name === 'Gyro');
        $this->assertEquals($this->gyro, $item);
    }

    #[Test]
    public function find_object_by_key(): void
    {
        $item = $this->collection()->pick(static fn(Person $person, int $key) => $key === 0);
        $this->assertEquals($this->jotaro, $item);
    }

    #[Test]
    public function each_objects(): void
    {
        $items = [];

        $this->collection()->each(function (Person $person, int $key) use (&$items) {
            $items[$key] = $person->name;
        });

        $this->assertEquals([0 => 'Jotaro', 1 => 'Gyro', 2 => 'Josuke'], $items);
    }

    #[Test]
    public function filter_object_by_value(): void
    {
        $items = $this->collection()->filter(static fn(Person $person, int $key) => $person->name === 'Josuke')->items();

        $this->assertEquals([2 => $this->josuke], $items);
    }

    #[Test]
    public function filter_object_by_key(): void
    {
        $items = $this->collection()->filter(static fn(Person $person, int $key) => $key === 1)->items();

        $this->assertEquals([1 => $this->gyro], $items);
    }

    #[Test]
    public function reduce_objects(): void
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

    #[Test]
    public function some_object_by_value(): void
    {
        $this->assertTrue($this->collection()->some(static fn(Person $person) => $person->name === 'Gyro'));
        $this->assertFalse($this->collection()->some(static fn(Person $person) => $person->name === 'Non'));
    }

    #[Test]
    public function some_object_by_key(): void
    {
        $this->assertTrue($this->collection()->some(static fn(Person $person, int $key) => $key === 1));
        $this->assertFalse($this->collection()->some(static fn(Person $person, int $key) => $key === 4));
    }

    #[Test]
    public function every_object_by_value(): void
    {
        $this->assertTrue($this->collection()->some(static fn(Person $person) => !empty($person->name)));
        $this->assertFalse($this->collection()->some(static fn(Person $person) => empty($person->name)));
    }

    #[Test]
    public function every_object_by_key(): void
    {
        $this->assertTrue($this->collection()->some(static fn(Person $person, mixed $key) => is_numeric($key)));
        $this->assertFalse($this->collection()->some(static fn(Person $person, mixed $key) => !is_numeric($key)));
    }

    #[Test]
    public function key_object_of(): void
    {
        $this->assertEquals(1, $this->collection()->keyOf(static fn(Person $person) => $person->name === 'Gyro'));
        $this->assertEquals(0, $this->collection()->keyOf(static fn(Person $person) => $person->name === 'Jotaro'));
    }

    #[Test]
    public function object_values(): void
    {
        $this->assertEquals([
            $this->jotaro,
            $this->gyro,
            $this->josuke,
        ], $this->collection()->values());
    }

    #[Test]
    public function merge_two_objects_collection_into_a_new_one(): void
    {
        $dio           = new Person('Dio');
        $newCollection = $this->collection()->merge(new StronglySecureList([$dio]));

        $this->assertEquals([
            $this->jotaro,
            $this->gyro,
            $this->josuke,
            $dio
        ], $newCollection->values());
    }

    #[Test]
    public function merging_collection_of_objects_of_different_types_throws_an_exception(): void
    {
        $this->expectException(InvalidType::class);
        $this->collection()->merge(new class extends SecureList {
            public function __construct()
            {
                parent::__construct([new stdClass()]);
            }

            protected static function type(): string
            {
                return stdClass::class;
            }
        });
    }

    #[Test]
    public function overlay_two_objects_collection_into_a_new_one(): void
    {
        $dio           = new Person('Dio');
        $newCollection = $this->collection()->overlay(new StronglySecureList([2 => $dio]));

        $this->assertEquals([
            $this->jotaro,
            $this->gyro,
            $dio
        ], $newCollection->values());
    }

    #[Test]
    public function overlay_collection_of_objects_of_different_types_throws_an_exception(): void
    {
        $this->expectException(InvalidType::class);
        $this->collection()->overlay(new class extends SecureList {
            public function __construct()
            {
                parent::__construct([new stdClass()]);
            }

            protected static function type(): string
            {
                return stdClass::class;
            }
        });
    }

    protected function setUp(): void
    {
        parent::setUp();
        StronglySecureList::setType(Person::class);
        $this->jotaro = Person::create('Jotaro');
        $this->gyro   = Person::create('Gyro');
        $this->josuke = Person::create('Josuke');
    }

    protected function collection(): StronglySecureList
    {
        return new StronglySecureList([
                $this->jotaro,
                $this->gyro,
                $this->josuke,
            ]
        );
    }
}
