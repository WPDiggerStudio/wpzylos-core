<?php

declare(strict_types=1);

namespace WPZylos\Framework\Core\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WPZylos\Framework\Core\PluginContext;

/**
 * Tests for PluginContext class.
 */
class PluginContextTest extends TestCase
{
    private PluginContext $context;

    protected function setUp(): void
    {
        $this->context = PluginContext::create([
            'file' => '/path/to/my-plugin/my-plugin.php',
            'slug' => 'my-plugin',
            'prefix' => 'myplugin_',
            'textDomain' => 'my-plugin',
            'version' => '1.0.0',
        ]);
    }

    public function testSlugReturnsConfiguredValue(): void
    {
        $this->assertSame('my-plugin', $this->context->slug());
    }

    public function testPrefixReturnsConfiguredValue(): void
    {
        $this->assertSame('myplugin_', $this->context->prefix());
    }

    public function testTextDomainReturnsConfiguredValue(): void
    {
        $this->assertSame('my-plugin', $this->context->textDomain());
    }

    public function testVersionReturnsConfiguredValue(): void
    {
        $this->assertSame('1.0.0', $this->context->version());
    }

    public function testFileReturnsConfiguredValue(): void
    {
        $this->assertSame('/path/to/my-plugin/my-plugin.php', $this->context->file());
    }

    public function testPathReturnsPluginRoot(): void
    {
        $path = $this->context->path();
        $this->assertStringEndsWith('my-plugin/', $path);
    }

    public function testPathAppendsRelativePath(): void
    {
        $path = $this->context->path('config/app.php');
        $this->assertStringEndsWith('config/app.php', $path);
    }

    public function testHookPrefixesCorrectly(): void
    {
        $this->assertSame('myplugin_settings_saved', $this->context->hook('settings_saved'));
    }

    public function testOptionKeyPrefixesCorrectly(): void
    {
        $this->assertSame('myplugin_version', $this->context->optionKey('version'));
    }
}
