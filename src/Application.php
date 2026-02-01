<?php

declare(strict_types=1);

namespace WPZylos\Framework\Core;

use Psr\Container\ContainerInterface;
use WPZylos\Framework\Core\Contracts\ApplicationInterface;
use WPZylos\Framework\Core\Contracts\ContextInterface;
use WPZylos\Framework\Core\Contracts\ServiceProviderInterface;

/**
 * Application kernel.
 *
 * The central service container and provider coordinator. Each plugin
 * has its own isolated Application instance.
 *
 * @package WPZylos\Framework\Core
 */
class Application implements ApplicationInterface
{
    /**
     * @var ContextInterface Plugin context
     */
    private ContextInterface $context;

    /**
     * @var ContainerInterface DI container
     */
    private ContainerInterface $container;

    /**
     * @var ServiceProviderInterface[] Registered providers
     */
    private array $providers = [];

    /**
     * @var bool Whether the application has booted
     */
    private bool $booted = false;

    /**
     * @var Paths Path resolver
     */
    private Paths $paths;

    /**
     * Create an application instance.
     *
     * @param ContextInterface $context Plugin context
     * @param ContainerInterface $container DI container
     */
    public function __construct(ContextInterface $context, ContainerInterface $container)
    {
        $this->context = $context;
        $this->container = $container;
        $this->paths = new Paths($context);

        $this->registerBaseBindings();
    }

    /**
     * Register core bindings.
     *
     * @return void
     */
    private function registerBaseBindings(): void
    {
        // Bind self
        $this->singleton(ApplicationInterface::class, fn() => $this);
        $this->singleton(static::class, fn() => $this);
        $this->singleton('app', fn() => $this);

        // Bind context
        $this->singleton(ContextInterface::class, fn() => $this->context);
        $this->singleton('context', fn() => $this->context);

        // Bind paths
        $this->singleton(Paths::class, fn() => $this->paths);
        $this->singleton('paths', fn() => $this->paths);
    }

    /**
     * {@inheritDoc}
     */
    public function context(): ContextInterface
    {
        return $this->context;
    }

    /**
     * {@inheritDoc}
     */
    public function container(): ContainerInterface
    {
        return $this->container;
    }

    /**
     * Get the paths' resolver.
     *
     * @return Paths
     */
    public function paths(): Paths
    {
        return $this->paths;
    }

    /**
     * {@inheritDoc}
     */
    public function register(ServiceProviderInterface $provider): static
    {
        $this->providers[] = $provider;
        $provider->register($this);

        // If already booted, boot this provider immediately
        if ($this->booted) {
            $provider->boot($this);
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function boot(): void
    {
        if ($this->booted) {
            return;
        }

        foreach ($this->providers as $provider) {
            $provider->boot($this);
        }

        $this->booted = true;
    }

    /**
     * {@inheritDoc}
     */
    public function isBooted(): bool
    {
        return $this->booted;
    }

    /**
     * {@inheritDoc}
     */
    public function make(string $abstract): mixed
    {
        return $this->container->get($abstract);
    }

    /**
     * {@inheritDoc}
     */
    public function bind(string $abstract, callable|string|null $concrete = null): void
    {
        if (method_exists($this->container, 'bind')) {
            $this->container->bind($abstract, $concrete);
        } elseif (method_exists($this->container, 'add')) {
            // League\Container style
            $this->container->add($abstract, $concrete);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function singleton(string $abstract, callable|string|null $concrete = null): void
    {
        if (method_exists($this->container, 'singleton')) {
            $this->container->singleton($abstract, $concrete);
        } elseif (method_exists($this->container, 'addShared')) {
            // League\Container style
            $this->container->addShared($abstract, $concrete);
        } elseif (method_exists($this->container, 'share')) {
            $this->container->add($abstract, $concrete)->share();
        }
    }

    /**
     * Get all registered providers.
     *
     * @return ServiceProviderInterface[]
     */
    public function getProviders(): array
    {
        return $this->providers;
    }

    /**
     * Check if a service is bound in the container.
     *
     * @param string $abstract Service identifier
     * @return bool
     */
    public function has(string $abstract): bool
    {
        return $this->container->has($abstract);
    }
}
