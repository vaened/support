<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Types;

use PHPUnit\Framework\Attributes\Test;
use stdClass;
use Vaened\Support\Tests\TestCase;
use Vaened\Support\Tests\Types\Utils\StronglySecureList;
use Vaened\Support\Types\InvalidSafelistItem;

use function sprintf;

final class ListTypesTest extends TestCase
{
    #[Test]
    public function valid_integer_list(): void
    {
        StronglySecureList::setType('integer');
        $list = new StronglySecureList([1, 2, 3]);
        $this->assertCount(3, $list);
    }

    #[Test]
    public function valid_string_list(): void
    {
        StronglySecureList::setType('string');
        $list = new StronglySecureList(['a', 'b', 'c']);
        $this->assertCount(3, $list);
    }

    #[Test]
    public function valid_float_list(): void
    {
        StronglySecureList::setType('double');
        $list = new StronglySecureList([1.2, 1.4, 1.6]);
        $this->assertCount(3, $list);
    }

    #[Test]
    public function valid_boolean_list(): void
    {
        StronglySecureList::setType('boolean');
        $list = new StronglySecureList([true, false, true]);
        $this->assertCount(3, $list);
    }

    #[Test]
    public function valid_array_list(): void
    {
        StronglySecureList::setType('array');
        $list = new StronglySecureList([[1, 2], [3, 4], [5, 6]]);
        $this->assertCount(3, $list);
    }

    #[Test]
    public function valid_object_list(): void
    {
        StronglySecureList::setType('object');
        $list = new StronglySecureList([new stdClass(), new stdClass(), new stdClass()]);
        $this->assertCount(3, $list);
    }

    #[Test]
    public function throw_exception_in_invalid_integer(): void
    {
        $this->expectTypeException('integer', 'invalid');
        new StronglySecureList(['invalid', 'type']);
    }

    #[Test]
    public function throw_exception_in_invalid_string(): void
    {
        $this->expectTypeException('string', '1.2');
        new StronglySecureList([1.2, 3.4]);
    }

    #[Test]
    public function throw_exception_in_invalid_float(): void
    {
        $this->expectTypeException('double', 'TRUE');
        new StronglySecureList([true, false]);
    }

    #[Test]
    public function throw_exception_in_invalid_bool(): void
    {
        $this->expectTypeException('boolean', 'stdClass');
        new StronglySecureList([new stdClass(), new stdClass()]);
    }

    #[Test]
    public function throw_exception_in_invalid_object(): void
    {
        $this->expectTypeException('object', 'invalid');
        new StronglySecureList(['invalid', 'type']);
    }

    private function expectTypeException(string $expected, string $actual): void
    {
        StronglySecureList::setType($expected);
        $template = 'The collection <%s> requires type <%s>, but <%s> was given';
        $this->expectException(InvalidSafelistItem::class);
        $this->expectExceptionMessage(
            sprintf($template, StronglySecureList::class, $expected, $actual)
        );
    }
}
