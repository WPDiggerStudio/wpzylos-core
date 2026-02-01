<?php

declare(strict_types=1);

namespace WPZylos\Framework\Core;

use WPZylos\Framework\Core\Contracts\ApplicationInterface;
use WPZylos\Framework\Core\Contracts\ServiceProviderInterface;

/**
 * Abstract service provider.
 *
 * Base class for all service providers. Extend this class and implement
 * the register() method to bind services to the container.
 *
 * @package WPZylos\Framework\Core
 */
abstract class ServiceProvider implements ServiceProviderInterface
{
    /**
     * @var ApplicationInterface The application instance
     */
    protected ApplicationInterface $app;

    /**
     * Register services with the container.
     *
     * This method is called during the registration phase.
     * Override in child classes to bind services.
     *
     * @param ApplicationInterface $app The application instance
     *
     * @return void
     */
    public function register(ApplicationInterface $app): void
    {
        $this->app = $app;
    }

    /**
     * Bootstrap services after all providers are registered.
     *
     * This method is called during the boot phase.
     * Override in child classes to perform boot-time setup.
     *
     * @param ApplicationInterface $app The application instance
     *
     * @return void
     */
    public function boot(ApplicationInterface $app): void
    {
        // Default implementation does nothing
    }

    /**
     * Bind a service to the container.
     *
     * @param string $abstract Service identifier
     * @param callable|string|null $concrete Concrete implementation
     *
     * @return void
     */
    protected function bind(string $abstract, callable|string|null $concrete = null): void
    {
        $this->app->bind($abstract, $concrete);
    }

    /**
     * Bind a singleton service to the container.
     *
     * @param string $abstract Service identifier
     * @param callable|string|null $concrete Concrete implementation
     *
     * @return void
     */
    protected function singleton(string $abstract, callable|string|null $concrete = null): void
    {
        $this->app->singleton($abstract, $concrete);
    }

    /**
     * Resolve a service from the container.
     *
     * @template T
     * @param class-string<T>|string $abstract Service identifier
     *
     * @return T|mixed
     */
    protected function make(string $abstract): mixed
    {
        return $this->app->make($abstract);
    }

    /**
     * Get the plugin context.
     *
     * @return \WPZylos\Framework\Core\Contracts\ContextInterface
     */
    protected function context(): Contracts\ContextInterface
    {
        return $this->app->context();
    }
}
