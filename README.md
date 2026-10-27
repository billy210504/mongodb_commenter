# MongoDB Commenter for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/billy210504/mongodb-commenter.svg?style=flat-square)](https://packagist.org/packages/billy210504/mongodb-commenter)
[![GitHub Tests Action Status](https://github.com/billy210504/mongodb_commenter/actions/workflows/run-tests.yml/badge.svg)](https://github.com/billy210504/mongodb_commenter/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/billy210504/mongodb-commenter.svg?style=flat-square)](https://packagist.org/packages/billy210504/mongodb-commenter)
[![License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](LICENSE.md)

**MongoDB Commenter** automatically attaches SQLCommenter-style contextual metadata (such as controller, action, route, user, artisan command, and application name) to your MongoDB Eloquent queries in Laravel.

This allows database administrators and monitoring tools (like **MongoDB Atlas Profiler**, **DataDog**, and **APM**) to trace queries back to the exact code location and user context that triggered them.

---

## Features

- ⚡ **Seamless Integration**: Simply add the `HasComments` trait to your MongoDB Eloquent models.
- 🏷️ **Contextual Metadata**: Automatically captures `controller`, `action`, `route`, `user`, `command`, and `app_name`.
- 🔀 **Invokable & Closure Support**: Accurately labels invokable controllers (`__invoke`) and closures.
- 🎯 **Configurable Tags**: Toggle specific metadata on or off via configuration or `.env` variables.
- 📋 **Deterministic Order**: Query comments are sorted alphabetically following the SQLCommenter specification.

---

## Installation

You can install the package via Composer:

```bash
composer require billy210504/mongodb-commenter
```

Publish and set up the configuration file using the install command:

```bash
php artisan mongodb-commenter:install
```

Or publish the config file directly:

```bash
php artisan vendor:publish --tag="mongodb-commenter-config"
```

---

## Configuration

The published configuration file is located at `config/mongodb-commenter.php`:

```php
return [
    /*
    |--------------------------------------------------------------------------
    | Enable/Disable MongoDB Query Commenter
    |--------------------------------------------------------------------------
    */
    'enabled' => env('MONGO_COMMENTER_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Custom Application Name (Optional)
    |--------------------------------------------------------------------------
    | If null, it will default to config('app.name').
    */
    'app_name' => env('MONGO_COMMENTER_APP_NAME', null),

    /*
    |--------------------------------------------------------------------------
    | Included Comment Metadata Tags
    |--------------------------------------------------------------------------
    */
    'include_app_name' => env('MONGO_COMMENTER_INCLUDE_APP_NAME', true),
    'include_controller' => env('MONGO_COMMENTER_CONTROLLER', true),
    'include_route' => env('MONGO_COMMENTER_ROUTE', true),
    'include_user' => env('MONGO_COMMENTER_USER', true),
    'include_command' => env('MONGO_COMMENTER_COMMAND', true),
];
```

---

## Usage

### 1. Attach `HasComments` to Your MongoDB Models

Add the `HasComments` trait to any of your MongoDB Eloquent models:

```php
namespace App\Models;

use Billy\MongodbCommenter\Traits\HasComments;
use MongoDB\Laravel\Eloquent\Model;

class Product extends Model
{
    use HasComments;

    protected $connection = 'mongodb';
    protected $collection = 'products';

    protected $fillable = [
        'name',
        'price',
        'stock',
    ];
}
```

### 2. Output Query Comment Examples

#### A. Web / API Request

When running a query inside an HTTP request (e.g. `GET /api/products` handled by `ProductController@index`):

```php
$products = Product::where('stock', '>', 0)->get();
```

The MongoDB query will execute with the following comment attached:

```text
action='index',app_name='Laravel',controller='ProductController',route='products.index',user='10482'
```

#### B. Artisan Console Command

When executing queries inside an Artisan command (e.g. `php artisan sync:products`):

```php
$products = Product::all();
```

The MongoDB query comment will look like:

```text
app_name='Laravel',command='sync:products'
```

---

## Testing

Run the test suite using Pest:

```bash
composer test
```

---

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Billy](https://github.com/billy210504)
- [Syahreza-Ferdian](https://github.com/Syahreza-Ferdian)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
