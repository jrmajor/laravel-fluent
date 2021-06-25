<?php

namespace Major\Fluent\Laravel\Tests;

use Major\Fluent\Laravel\FluentServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [FluentServiceProvider::class];
    }

    protected function resolveApplicationBootstrappers($app): void
    {
        $app->instance('path.lang', __DIR__.'/lang');

        $app['config']->set([
            'app.locale' => 'pl',
            'app.fallback_locale' => 'en',
        ]);

        parent::resolveApplicationBootstrappers($app);
    }
}
