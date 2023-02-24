# jrmajor/laravel-fluent

<a href="https://packagist.org/packages/jrmajor/laravel-fluent"><img src="https://img.shields.io/packagist/v/jrmajor/laravel-fluent.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/jrmajor/laravel-fluent"><img src="https://img.shields.io/packagist/php-v/jrmajor/laravel-fluent.svg" alt="Required PHP Version"></a>

Unleash the expressive power of the natural language in your Laravel application with [Project Fluent](https://projectfluent.org), a localization system designed by Mozilla.

Read the [Fluent Syntax Guide](https://projectfluent.org/fluent/guide/) or try it out in the [Fluent Playground](https://projectfluent.org/play/) to learn more about the syntax.

```ftl
shared-photos =
    { $userName } { $photoCount ->
        [one] added a new photo
       *[other] added { $photoCount } new photos
    } to { $userGender ->
        [male] his stream
        [female] her stream
       *[other] their stream
    }.
```

```php
__('stream.shared-photos', [
    'userName' => 'jrmajor',
    'photoCount' => 2,
    'userGender' => 'male',
]); // jrmajor added 2 new photos to his stream.
```

This package is a Laravel wrapper around [jrmajor/fluent-php](https://github.com/jrmajor/fluent-php). The current version supports Laravel 9 and 10.

You may install it via Composer: `composer require jrmajor/laravel-fluent`. The package will automatically register itself.

## Usage

This package replaces default Laravel translator with `Major\Fluent\Laravel\FluentTranslator`.

```php
app('translator') instanceof Major\Fluent\Laravel\FluentTranslator; // true
```

Fluent translations are stored in `.ftl` files. Place them among your `.php` translation files in your Laravel app:

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

If there is no Fluent message for a given key, translator will fall back to a `.php` file, which allows you to introduce Fluent translation format progressively.

Laravel validator uses custom logic for replacing the `:attribute` variable and requires deeply nested keys, which are not supported in Fluent, so you should leave `validation.php` file in the default Laravel format.

The `trans_choice()` helper always falls back to the default translator, as the Fluent format eliminates the need for this function.

You may use the `FluentTranslator::addFunction()` method to register [Fluent functions](https://projectfluent.org/fluent/guide/functions.html).

### Configuration

Optionally, you can publish the configuration file with this command:

```php
php artisan vendor:publish --tag fluent-config
```

This will publish the following file in `config/fluent.php`:

```php
return [

    /*
     * In strict mode, exceptions will be thrown for syntax errors
     * in .ftl files, unknown variables in messages etc.
     * It's recommended to enable this setting in development
     * to make it easy to spot mistakes.
     */
    'strict' => env('APP_ENV', 'production') !== 'production',

    /*
     * Determines if it should use Unicode isolation marks (FSI, PDI)
     * for bidirectional interpolations. You may want to enable this
     * behaviour if your application uses right-to-left script.
     */
    'use_isolating' => false,

];
```

## Testing

```sh
vendor/bin/phpunit           # Tests
vendor/bin/phpstan analyse   # Static analysis
vendor/bin/php-cs-fixer fix  # Formatting
```
