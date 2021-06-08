<?php

use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Application;
use Illuminate\Translation\Translator as BaseTranslator;
use Major\Fluent\Laravel\FluentTranslator;

it('tries to load a non-existent file only once', function () {
    $app = Application::getInstance();

    $app->forgetInstance('translator');
    $app->forgetExtenders('translator');

    $app->extend(
        'translator',
        fn (BaseTranslator $service, Application $app) => new FluentTranslator(
            baseTranslator: $service,
            files: Mockery::mock(Filesystem::class)
                ->shouldReceive('exists')->twice()->andReturn(false)
                ->getMock(),
            path: $app['path.lang'],
            locale: 'pl', fallback: 'en',
            bundleOptions: ['strict' => true, 'useIsolating' => false],
        ),
    );

    __('missing.message');

    __('missing.message');
});
