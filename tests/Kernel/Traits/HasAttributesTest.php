<?php

namespace EasyWeChat\Tests\Kernel\Traits;

use ArrayAccess;
use EasyWeChat\Kernel\Traits\HasAttributes;
use EasyWeChat\Tests\TestCase;

class HasAttributesTest extends TestCase
{
    public function test_missing_offset_returns_null_without_warnings()
    {
        $message = new DummyClassForHasAttributesTest;
        $errors = [];

        set_error_handler(function (int $severity, string $message) use (&$errors): bool {
            $errors[] = [$severity, $message];

            return true;
        });

        try {
            $this->assertNull($message['missing']);
        } finally {
            restore_error_handler();
        }

        $this->assertSame([], $errors);
    }

    public function test_existing_null_offset_is_still_accessible()
    {
        $message = new DummyClassForHasAttributesTest(['foo' => null]);

        $this->assertTrue(isset($message['foo']));
        $this->assertNull($message['foo']);
    }
}

class DummyClassForHasAttributesTest implements \JsonSerializable, ArrayAccess
{
    use HasAttributes;

    /**
     * @param  array<int|string, mixed>  $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }
}
