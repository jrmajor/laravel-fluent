<?php

namespace Major\Fluent\Laravel;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Illuminate\Translation\TranslationServiceProvider;
use Illuminate\Translation\Translator as BaseTranslator;

final class FluentServiceProvider extends ServiceProvider
{
    /** @var Application */
    protected $app;

    public function __construct($app)
    {
        assert($app instanceof Application);

        parent::__construct($app);
    }

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

        $this->app->singleton('translator', function (Application $app) {
            /** @phpstan-ignore-next-line */
            $options = $app['config']['fluent'];

            assert(
                is_array($options)
                && is_bool($options['strict'])
                && is_bool($options['use_isolating'])
                && is_bool($options['allow_overrides']),
            );

            return new FluentTranslator(
                baseTranslator: $app[BaseTranslator::class], /** @phpstan-ignore-line */
                files: $app[Filesystem::class],              /** @phpstan-ignore-line */
                path: $app['path.lang'],                     /** @phpstan-ignore-line */
                locale: $app->getLocale(),
                fallback: $app->getFallbackLocale(),
                bundleOptions: [
                    'strict' => $options['strict'],
                    'useIsolating' => $options['use_isolating'],
                    'allowOverrides' => $options['allow_overrides'],
                ],
            );
        });

        $this->app->alias('translator', FluentTranslator::class);
    }
}
