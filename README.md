
# BytePlatform\Laravel is Laravel PackageKit 

[![Latest Version on Packagist](https://img.shields.io/packagist/v/byteplatform/laravel.svg?style=flat-square)](https://packagist.org/packages/byteplatform/laravel)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/byteplatform/laravel/run-tests?label=tests)](https://github.com/byteplatform/laravel/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/byteplatform/laravel/Fix%20PHP%20code%20style%20issues?label=code%20style)](https://github.com/byteplatform/laravel/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/byteplatform/laravel.svg?style=flat-square)](https://packagist.org/packages/byteplatform/laravel)

This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.


## Installation

You can install the package via composer:

```bash
composer require byteplatform/laravel
```

```php
//
use Illuminate\Support\ServiceProvider;
use BytePlatform\Laravel\ServicePackage;
use BytePlatform\Laravel\Traits\WithServiceProvider;

class DemoServiceProvider extends ServiceProvider
{
    use WithServiceProvider;

    public function configurePackage(ServicePackage $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         */
        $package
            ->name('demo')
            ->hasConfigFile()
            ->hasViews()
            ->hasHelpers()
            ->hasAssets()
            ->hasTranslations()
            ->runsMigrations();
    }
    public function extending()
    {
    }
    public function packageRegistered()
    {
        $this->extending();
    }
}

```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [ByTech Asia](https://github.com/ByTechAsia)
- [BytePlatform](https://github.com/byteplatform)
- [Nguyen Van Hau](https://github.com/devhau)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
