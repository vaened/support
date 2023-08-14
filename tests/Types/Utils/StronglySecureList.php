<?php
/**
 * @author enea dhack <enea.so@live.com>
 */

declare(strict_types=1);

namespace Vaened\Support\Tests\Types\Utils;

use Vaened\Support\Types\SecureList;

final class StronglySecureList extends SecureList
{
    private static string $type;

    public function __construct(array $items)
    {
        parent::__construct($items);
    }

    public static function setType(string $type): void
    {
        self::$type = $type;
    }

    protected static function type(): string
    {
        return self::$type;
    }
}
