<?php

namespace Major\Fluent\Laravel;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Application as App;
use Illuminate\Support\ServiceProvider;
use Illuminate\Translation\TranslationServiceProvider;
use Illuminate\Translation\Translator as BaseTranslator;

/**
 * @property App $app
 */
final class FluentServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/fluent.php' => config_path('fluent.php'),
        ], 'fluent-config');
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/fluent.php', 'fluent');

        // We need to force register the Laravel translator provider, so that
        // we can obtain an instance of BaseTranslator. Normally service providers
        // under Illuminate\ namespace are loaded first, but this one is deferred.
        if (! $this->app->providerIsLoaded(TranslationServiceProvider::class)) {
            $this->app->registerDeferredProvider(TranslationServiceProvider::class);
        }

        // BaseTranslator is an alias to 'translator'. Setting an instance is the only way
        // to remove an alias, so if we don't do this before overwriting 'translator',
        // there will be no way to resolve BaseTranslator from the container.
        $this->app->instance(BaseTranslator::class, $this->app[BaseTranslator::class]);

        $this->app->singleton('translator', fn (App $app) => new FluentTranslator(
            baseTranslator: $app[BaseTranslator::class],
            files: $app[Filesystem::class],
            path: $app['path.lang'],
            locale: $app->getLocale(),
            fallback: $app->getFallbackLocale(),
            bundleOptions: [
                'strict' => $app['config']['fluent.strict'],
                'useIsolating' => $app['config']['fluent.use_isolating'],
            ],
        ));

        $this->app->alias('translator', FluentTranslator::class);
    }
}
