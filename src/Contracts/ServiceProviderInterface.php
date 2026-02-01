<?php

declare(strict_types=1);

namespace WPZylos\Framework\Core\Contracts;

/**
 * Service provider interface.
 *
 * Service providers are the central place to configure and bootstrap
 * application services. They are registered with the Application and
 * called at appropriate lifecycle points.
 *
 * @package WPZylos\Framework\Core
 */
interface ServiceProviderInterface
{
    /**
     * Register services with the container.
     *
     * This method is called during the registration phase.
     * Use it to bind services to the container.
     *
     * @param ApplicationInterface $app The application instance
     *
     * @return void
     */
    public function register(ApplicationInterface $app): void;

    /**
     * Bootstrap services after all providers are registered.
     *
     * This method is called during the boot phase, after all providers
     * have been registered. Use it to perform actions that depend on
     * other services being available.
     *
     * @param ApplicationInterface $app The application instance
     *
     * @return void
     */
    public function boot(ApplicationInterface $app): void;
}
