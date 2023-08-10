<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Types;

use PHPUnit\Framework\Attributes\Test;
use stdClass;
use Vaened\Support\Tests\TestCase;
use Vaened\Support\Tests\Types\Utils\StronglyTypedList;
use Vaened\Support\Types\InvalidType;

use function sprintf;

final class ListTypesTest extends TestCase
{
    #[Test]
    public function valid_integer_list(): void
    {
        StronglyTypedList::setType('integer');
        $list = new StronglyTypedList([1, 2, 3]);
        $this->assertCount(3, $list);
    }

    #[Test]
    public function valid_string_list(): void
    {
        StronglyTypedList::setType('string');
        $list = new StronglyTypedList(['a', 'b', 'c']);
        $this->assertCount(3, $list);
    }

    #[Test]
    public function valid_float_list(): void
    {
        StronglyTypedList::setType('double');
        $list = new StronglyTypedList([1.2, 1.4, 1.6]);
        $this->assertCount(3, $list);
    }

    #[Test]
    public function valid_boolean_list(): void
    {
        StronglyTypedList::setType('boolean');
        $list = new StronglyTypedList([true, false, true]);
        $this->assertCount(3, $list);
    }

    #[Test]
    public function valid_array_list(): void
    {
        StronglyTypedList::setType('array');
        $list = new StronglyTypedList([[1, 2], [3, 4], [5, 6]]);
        $this->assertCount(3, $list);
    }

    #[Test]
    public function valid_object_list(): void
    {
        StronglyTypedList::setType('object');
        $list = new StronglyTypedList([new stdClass(), new stdClass(), new stdClass()]);
        $this->assertCount(3, $list);
    }

    #[Test]
    public function throw_exception_in_invalid_integer(): void
    {
        $this->expectTypeException('integer', 'invalid');
        new StronglyTypedList(['invalid', 'type']);
    }

    #[Test]
    public function throw_exception_in_invalid_string(): void
    {
        $this->expectTypeException('string', '1.2');
        new StronglyTypedList([1.2, 3.4]);
    }

    #[Test]
    public function throw_exception_in_invalid_float(): void
    {
        $this->expectTypeException('double', 'TRUE');
        new StronglyTypedList([true, false]);
    }

    #[Test]
    public function throw_exception_in_invalid_bool(): void
    {
        $this->expectTypeException('boolean', 'stdClass');
        new StronglyTypedList([new stdClass(), new stdClass()]);
    }

    #[Test]
    public function throw_exception_in_invalid_object(): void
    {
        $this->expectTypeException('object', 'invalid');
        new StronglyTypedList(['invalid', 'type']);
    }

    private function expectTypeException(string $expected, string $actual): void
    {
        StronglyTypedList::setType($expected);
        $template = 'The collection <%s> requires type <%s>, but <%s> was given';
        $this->expectException(InvalidType::class);
        $this->expectExceptionMessage(
            sprintf($template, StronglyTypedList::class, $expected, $actual)
        );
    }
}
