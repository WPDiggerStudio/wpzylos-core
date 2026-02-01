<?php

declare(strict_types=1);

namespace WPZylos\Framework\Core\Tests\Unit\Support;

use PHPUnit\Framework\TestCase;
use WPZylos\Framework\Core\Support\Str;

/**
 * Tests for Str utility class.
 */
class StrTest extends TestCase
{
    public function testStudlyConvertsSnakeCase(): void
    {
        $this->assertSame('HelloWorld', Str::studly('hello_world'));
    }

    public function testStudlyConvertsKebabCase(): void
    {
        $this->assertSame('HelloWorld', Str::studly('hello-world'));
    }

    public function testCamelConvertsSnakeCase(): void
    {
        $this->assertSame('helloWorld', Str::camel('hello_world'));
    }

    public function testSnakeConvertsStudlyCase(): void
    {
        $this->assertSame('hello_world', Str::snake('HelloWorld'));
    }

    public function testKebabConvertsStudlyCase(): void
    {
        $this->assertSame('hello-world', Str::kebab('HelloWorld'));
    }

    public function testSlugCreatesUrlFriendlyString(): void
    {
        $this->assertSame('hello-world', Str::slug('Hello World'));
    }

    public function testContainsReturnsTrueWhenFound(): void
    {
        $this->assertTrue(Str::contains('Hello World', 'World'));
    }

    public function testContainsReturnsFalseWhenNotFound(): void
    {
        $this->assertFalse(Str::contains('Hello World', 'Universe'));
    }

    public function testStartsWithReturnsTrueForMatch(): void
    {
        $this->assertTrue(Str::startsWith('Hello World', 'Hello'));
    }

    public function testEndsWithReturnsTrueForMatch(): void
    {
        $this->assertTrue(Str::endsWith('Hello World', 'World'));
    }
}
