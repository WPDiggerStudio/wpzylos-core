<?php

declare(strict_types=1);

namespace WPZylos\Framework\Core\Tests\Unit\Support;

use PHPUnit\Framework\TestCase;
use WPZylos\Framework\Core\Support\Arr;

/**
 * Tests for Arr utility class.
 */
class ArrTest extends TestCase
{
    public function testGetRetrievesTopLevelValue(): void
    {
        $array = ['key' => 'value'];
        $this->assertSame('value', Arr::get($array, 'key'));
    }

    public function testGetRetrievesNestedValue(): void
    {
        $array = ['a' => ['b' => ['c' => 'deep']]];
        $this->assertSame('deep', Arr::get($array, 'a.b.c'));
    }

    public function testGetReturnsDefaultForMissingKey(): void
    {
        $array = ['a' => 1];
        $this->assertSame('default', Arr::get($array, 'missing', 'default'));
    }

    public function testGetReturnsNullForMissingKeyWithNoDefault(): void
    {
        $array = ['a' => 1];
        $this->assertNull(Arr::get($array, 'missing'));
    }

    public function testHasReturnsTrueForExistingKey(): void
    {
        $array = ['a' => ['b' => 1]];
        $this->assertTrue(Arr::has($array, 'a.b'));
    }

    public function testHasReturnsFalseForMissingKey(): void
    {
        $array = ['a' => 1];
        $this->assertFalse(Arr::has($array, 'b'));
    }

    public function testOnlyReturnsSubsetOfArray(): void
    {
        $array = ['a' => 1, 'b' => 2, 'c' => 3];
        $this->assertSame(['a' => 1, 'c' => 3], Arr::only($array, ['a', 'c']));
    }

    public function testExceptRemovesSpecifiedKeys(): void
    {
        $array = ['a' => 1, 'b' => 2, 'c' => 3];
        $this->assertSame(['a' => 1, 'c' => 3], Arr::except($array, ['b']));
    }
}
