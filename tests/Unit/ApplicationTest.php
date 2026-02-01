<?php

declare(strict_types=1);

namespace WPZylos\Framework\Core\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WPZylos\Framework\Core\Application;
use WPZylos\Framework\Core\Contracts\ApplicationInterface;
use WPZylos\Framework\Core\Contracts\ContextInterface;
use WPZylos\Framework\Core\Contracts\ServiceProviderInterface;
use WPZylos\Framework\Core\Paths;
use Psr\Container\ContainerInterface;

/**
 * Application unit tests.
 *
 * Tests the Application kernel lifecycle, service provider registration,
 * and container delegation.
 */
class ApplicationTest extends TestCase
{
    private ContextInterface $context;
    private ContainerInterface $container;

    protected function setUp(): void
    {
        $this->context = $this->createMock(ContextInterface::class);
        $this->context->method('slug')->willReturn('test-plugin');
        $this->context->method('prefix')->willReturn('tp_');
        $this->context->method('file')->willReturn('/path/to/plugin.php');

        $this->container = $this->createMockContainer();
    }

    private function createMockContainer(): ContainerInterface
    {
        $bindings = [];

        $container = $this->createMock(ContainerInterface::class);

        // Mock the has method
        $container->method('has')->willReturnCallback(
            fn($id) => isset($bindings[$id])
        );

        // Mock the get method 
        $container->method('get')->willReturnCallback(
            fn($id) => $bindings[$id] ?? null
        );

        // Add bind method if it exists
        if (method_exists($container, 'bind')) {
            $container->method('bind')->willReturnCallback(
                function ($id, $concrete) use (&$bindings) {
                    $bindings[$id] = is_callable($concrete) ? $concrete() : $concrete;
                }
            );
        }

        return $container;
    }

    public function testConstructorRegistersBaseBindings(): void
    {
        $app = new Application($this->context, $this->container);

        $this->assertInstanceOf(Application::class, $app);
        $this->assertSame($this->context, $app->context());
        $this->assertSame($this->container, $app->container());
    }

    public function testContextReturnsPluginContext(): void
    {
        $app = new Application($this->context, $this->container);

        $this->assertSame($this->context, $app->context());
    }

    public function testContainerReturnsContainer(): void
    {
        $app = new Application($this->context, $this->container);

        $this->assertSame($this->container, $app->container());
    }

    public function testPathsReturnsPaths(): void
    {
        $app = new Application($this->context, $this->container);

        $this->assertInstanceOf(Paths::class, $app->paths());
    }

    public function testIsBootedReturnsFalseBeforeBoot(): void
    {
        $app = new Application($this->context, $this->container);

        $this->assertFalse($app->isBooted());
    }

    public function testBootSetsBootedFlag(): void
    {
        $app = new Application($this->context, $this->container);

        $app->boot();

        $this->assertTrue($app->isBooted());
    }

    public function testBootOnlyRunsOnce(): void
    {
        $app = new Application($this->context, $this->container);
        $provider = $this->createMock(ServiceProviderInterface::class);

        // Register should be called once
        $provider->expects($this->once())->method('register');
        // Boot should be called once
        $provider->expects($this->once())->method('boot');

        $app->register($provider);
        $app->boot();
        $app->boot(); // Second boot should be no-op
    }

    public function testRegisterCallsProviderRegister(): void
    {
        $app = new Application($this->context, $this->container);
        $provider = $this->createMock(ServiceProviderInterface::class);

        $provider->expects($this->once())
            ->method('register')
            ->with($app);

        $app->register($provider);
    }

    public function testRegisterReturnsApplication(): void
    {
        $app = new Application($this->context, $this->container);
        $provider = $this->createMock(ServiceProviderInterface::class);

        $result = $app->register($provider);

        $this->assertSame($app, $result);
    }

    public function testBootCallsProviderBoot(): void
    {
        $app = new Application($this->context, $this->container);
        $provider = $this->createMock(ServiceProviderInterface::class);

        $provider->expects($this->once())
            ->method('boot')
            ->with($app);

        $app->register($provider);
        $app->boot();
    }

    public function testLateRegisteredProviderBootsImmediately(): void
    {
        $app = new Application($this->context, $this->container);

        // Boot first
        $app->boot();

        // Register after boot
        $provider = $this->createMock(ServiceProviderInterface::class);
        $provider->expects($this->once())->method('register');
        $provider->expects($this->once())->method('boot');

        $app->register($provider);
    }

    public function testGetProvidersReturnsAllProviders(): void
    {
        $app = new Application($this->context, $this->container);

        $provider1 = $this->createMock(ServiceProviderInterface::class);
        $provider2 = $this->createMock(ServiceProviderInterface::class);

        $app->register($provider1);
        $app->register($provider2);

        $providers = $app->getProviders();

        $this->assertCount(2, $providers);
        $this->assertContains($provider1, $providers);
        $this->assertContains($provider2, $providers);
    }

    public function testMakeDelegatesToContainer(): void
    {
        $expected = new \stdClass();

        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')
            ->with('SomeService')
            ->willReturn($expected);

        $app = new Application($this->context, $container);

        $this->assertSame($expected, $app->make('SomeService'));
    }

    public function testHasDelegatesToContainer(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->method('has')
            ->with('SomeService')
            ->willReturn(true);

        $app = new Application($this->context, $container);

        $this->assertTrue($app->has('SomeService'));
    }
}
