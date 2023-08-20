<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Types;

use PHPUnit\Framework\Attributes\Test;
use stdClass;
use Vaened\Support\PhunctionalList;
use Vaened\Support\Tests\TestCase;
use Vaened\Support\Tests\Types\Utils\Person;
use Vaened\Support\Types\ArrayList;

final class PhunctionalListTest extends TestCase
{
    #[Test]
    public function same_comparison(): void
    {
        $numbers = new ArrayList([1, 2, 3]);

        $this->assertList([1 => 2], $numbers->filter(PhunctionalList::same(2)));
        $this->assertEmpty($numbers->filter(PhunctionalList::same('2')));
    }

    #[Test]
    public function equals_comparison(): void
    {
        $numbers = new ArrayList(['1', '2', '3']);

        $this->assertList([1 => '2'], $numbers->filter(PhunctionalList::equals(2)));
        $this->assertList([1 => '2'], $numbers->filter(PhunctionalList::equals('2')));
        $this->assertEquals('2', $numbers->pick(PhunctionalList::equals(2)));
    }

    #[Test]
    public function different_comparison(): void
    {
        $numbers = new ArrayList([1, 2, 3]);

        $this->assertList([0 => 1, 2 => 3], $numbers->filter(PhunctionalList::different(2)));
    }

    #[Test]
    public function in_comparison(): void
    {
        $numbers = new ArrayList([1, 2, 3]);

        $this->assertList([1 => 2, 2 => 3], $numbers->filter(PhunctionalList::in([2, 3])));
    }

    #[Test]
    public function null_comparison(): void
    {
        $numbers = new ArrayList([1, null, 3]);

        $this->assertTrue($numbers->some(PhunctionalList::null()));
        $this->assertCount(1, $numbers->filter(PhunctionalList::null()));
    }

    #[Test]
    public function not_null_comparison(): void
    {
        $numbers = new ArrayList([1, 2, 3]);

        $this->assertList([1, 2, 3], $numbers->filter(PhunctionalList::notNull()));
    }

    #[Test]
    public function instance_of_comparison(): void
    {
        $list = new ArrayList([new Person('1'), new stdClass(), new Person('3')]);

        $this->assertList(
            [0 => new Person('1'), 2 => new Person('3')],
            $list->filter(PhunctionalList::instanceOf(Person::class))
        );
    }

    #[Test]
    public function not_instance_of_comparison(): void
    {
        $list = new ArrayList([new Person('1'), new stdClass(), new Person('3')]);

        $this->assertList(
            [0 => new Person('1'), 2 => new Person('3')],
            $list->filter(PhunctionalList::notInstanceOf(stdClass::class))
        );
    }
}
