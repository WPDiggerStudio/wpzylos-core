# WPZylos Core

[![PHP Version](https://img.shields.io/badge/php-%5E8.0-blue)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-green)](LICENSE)
[![GitHub](https://img.shields.io/badge/GitHub-WPDiggerStudio-181717?logo=github)](https://github.com/WPDiggerStudio/wpzylos-core)

The foundation package for WPZylos framework. Provides core interfaces, context management, and base classes for building WordPress plugins with modern architecture.

📖 **[Full Documentation](https://wpzylos.com)** | 🐛 **[Report Issues](https://github.com/WPDiggerStudio/wpzylos-core/issues)**

---

## ✨ Features

- **ContextInterface** — Plugin identity contract that survives PHP-Scoper
- **PluginContext** — Default implementation with prefixing, paths, options
- **Application** — Plugin kernel with service provider registration
- **ServiceProvider** — Base class for modular service registration
- **Paths** — Path resolution with named aliases
- **Utilities** — Arr and Str helper classes

---

## 📋 Requirements

| Requirement | Version |
| ----------- | ------- |
| PHP         | ^8.0    |
| WordPress   | 6.0+    |

---

## 🚀 Installation

```bash
composer require wpdiggerstudio/wpzylos-core
```

---

## 📖 Quick Start

```php
use WPZylos\Framework\Core\Application;
use WPZylos\Framework\Core\PluginContext;

// Create plugin context
$context = PluginContext::create([
    'file'       => __FILE__,
    'slug'       => 'my-plugin',
    'prefix'     => 'myplugin_',
    'textDomain' => 'my-plugin',
    'version'    => '1.0.0',
]);

// Create and boot application
$app = new Application($context);
$app->boot();
```

---

## 🏗️ Core Components

### PluginContext

Holds plugin identity and configuration:

```php
$context = PluginContext::create([
    'file'       => __FILE__,
    'slug'       => 'my-plugin',
    'prefix'     => 'myplugin_',
    'textDomain' => 'my-plugin',
    'version'    => '1.0.0',
]);

// Access properties
$context->slug();       // 'my-plugin'
$context->prefix();     // 'myplugin_'
$context->textDomain(); // 'my-plugin'
$context->version();    // '1.0.0'

// Prefix helpers
$context->prefixedOption('setting');  // 'myplugin_setting'
$context->prefixedHook('init');       // 'myplugin_init'
```

### Application

Plugin kernel that manages service providers:

```php
$app = new Application($context);

// Register service providers
$app->register(new DatabaseServiceProvider());
$app->register(new RoutingServiceProvider());

// Boot the application
$app->boot();

// Access the container
$service = $app->get(MyService::class);
```

### ServiceProvider

Base class for modular service registration:

```php
class MyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(MyService::class, function ($app) {
            return new MyService($app->get('config'));
        });
    }

    public function boot(): void
    {
        // Called after all providers registered
    }
}
```

### Paths

Path resolution with named aliases:

```php
$paths = new Paths(__DIR__);

// Register aliases
$paths->alias('views', 'resources/views');
$paths->alias('config', 'config');

// Resolve paths
$paths->get('views');        // /plugin/resources/views
$paths->get('config/app');   // /plugin/config/app
```

---

## 📦 Related Packages

| Package                                                                  | Description                 |
| ------------------------------------------------------------------------ | --------------------------- |
| [wpzylos-container](https://github.com/WPDiggerStudio/wpzylos-container) | PSR-11 dependency injection |
| [wpzylos-config](https://github.com/WPDiggerStudio/wpzylos-config)       | Configuration management    |
| [wpzylos-hooks](https://github.com/WPDiggerStudio/wpzylos-hooks)         | WordPress hook management   |
| [wpzylos-scaffold](https://github.com/WPDiggerStudio/wpzylos-scaffold)   | Plugin template             |

---

## 📖 Documentation

For comprehensive documentation, tutorials, and API reference, visit **[wpzylos.com](https://wpzylos.com)**.

---

## ☕ Support the Project

If you find this package helpful, consider buying me a coffee! Your support helps maintain and improve the WPZylos ecosystem.

<a href="https://www.paypal.com/donate/?hosted_button_id=66U4L3HG4TLCC" target="_blank">
  <img src="https://img.shields.io/badge/Donate-PayPal-blue.svg?style=for-the-badge&logo=paypal" alt="Donate with PayPal" />
</a>

---

## 📄 License

MIT License. See [LICENSE](LICENSE) for details.

---

## 🤝 Contributing

Contributions are welcome! Please see [CONTRIBUTING.md](CONTRIBUTING.md) for guidelines.

---

**Made with ❤️ by [WPDiggerStudio](https://github.com/WPDiggerStudio)**
