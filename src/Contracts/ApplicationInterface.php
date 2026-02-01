<?php

declare(strict_types=1);

namespace WPZylos\Framework\Core\Contracts;

use Psr\Container\ContainerInterface;

/**
 * Application interface.
 *
 * The Application is the kernel of the framework. Each plugin has its own
 * isolated Application instance with a private container.
 *
 * @package WPZylos\Framework\Core
 */
interface ApplicationInterface
{
    /**
     * Get the plugin context.
     *
     * @return ContextInterface The plugin context
     */
    public function context(): ContextInterface;

    /**
     * Get the dependency injection container.
     *
     * @return ContainerInterface The PSR-11 container
     */
    public function container(): ContainerInterface;

    /**
     * Register a service provider.
     *
     * @param ServiceProviderInterface $provider The provider to register
     *
     * @return static
     */
    public function register(ServiceProviderInterface $provider): static;

    /**
     * Boot the application and all registered providers.
     *
     * @return void
     */
    public function boot(): void;

    /**
     * Check if the application has been booted.
     *
     * @return bool True if booted
     */
    public function isBooted(): bool;

    /**
     * Resolve a service from the container.
     *
     * @template T
     * @param class-string<T>|string $abstract The service identifier
     *
     * @return T|mixed The resolved service
     */
    public function make(string $abstract): mixed;

    /**
     * Bind a service to the container.
     *
     * @param string $abstract The service identifier
     * @param callable|string|null $concrete The concrete implementation
     *
     * @return void
     */
    public function bind(string $abstract, callable|string|null $concrete = null): void;

    /**
     * Bind a singleton service to the container.
     *
     * @param string $abstract The service identifier
     * @param callable|string|null $concrete The concrete implementation
     *
     * @return void
     */
    public function singleton(string $abstract, callable|string|null $concrete = null): void;
}
