# jrmajor/laravel-fluent

<a href="https://packagist.org/packages/jrmajor/laravel-fluent"><img src="https://img.shields.io/packagist/v/jrmajor/laravel-fluent.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/jrmajor/laravel-fluent"><img src="https://img.shields.io/packagist/php-v/jrmajor/laravel-fluent.svg" alt="Required PHP Version"></a>

Unleash the expressive power of the natural language in your Laravel application. See [Fluent Syntax Guide](https://projectfluent.org/fluent/guide/) to learn Fluent syntax.

This package is a Laravel wrapper around [jrmajor/fluent-php](https://github.com/jrmajor/fluent-php).

```ftl
shared-photos =
    {$userName} {$photoCount ->
        [one] added a new photo
       *[other] added {$photoCount} new photos
    } to {$userGender ->
        [male] his stream
        [female] her stream
       *[other] their stream
    }.
```

```php
__('pagination.shared-photos', [
    'userName' => 'jrmajor',
    'photoCount' => 2,
    'userGender' => 'male',
]); // jrmajor added 2 new photos to his stream.
```

You can install it via Composer: `composer require jrmajor/laravel-fluent`. It requires PHP 8.0 and Laravel 8.0 or higher.

## Usage

Fluent translations are stored in `.ftl` files. Place them along your `.php` translation files in your Laravel app:

```
/resources
  /lang
    /en
      menu.ftl
      validation.php
    /pl
      menu.ftl
      validation.php
```

If there is no Fluent message for given key, translator will fall back to `.php` file, which allows you to introduce Fluent translation format progressively. Laravel validator uses custom logic for replacing `:attribute` variable and requires deeply nested keys, which are not supported in Fluent, so you should leave `validation.php` file in default Laravel format.

## Testing

```shell
# Tests
vendor/bin/pest

# Static analysis
vendor/bin/phpstan analyse
```
