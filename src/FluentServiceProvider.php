<?php

namespace Major\Fluent\Laravel;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Illuminate\Translation\Translator as BaseTranslator;

final class FluentServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/fluent.php' => config_path('fluent.php'),
        ], 'config');
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/fluent.php', 'fluent');

        $this->app->extend(
            BaseTranslator::class,
            fn (BaseTranslator $service, Application $app) => new FluentTranslator(
                baseTranslator: $service,
                files: $app['files'],
                path: $app['path.lang'],
                locale: $app->getLocale(),
                fallback: $app->getFallbackLocale(),
                bundleOptions: [
                    'strict' => $app['config']['fluent.strict'],
                    'useIsolating' => $app['config']['fluent.use_isolating'],
                ],
            ),
        );

        $this->app->alias('translator', FluentTranslator::class);
    }
}
