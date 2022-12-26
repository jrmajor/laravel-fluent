<?php

namespace Major\Fluent\Laravel\Tests;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Translation\Translator as BaseTranslator;
use Major\Fluent\Laravel\FluentTranslator;
use Mockery;

final class LoaderTest extends TestCase
{
    /**
     * @testdox it tries to load a non-existent file only once
     */
    public function testLoadsOnlyOnce(): void
    {
        $translator = new FluentTranslator(
            baseTranslator: app(BaseTranslator::class),
            files: Mockery::mock(Filesystem::class)
                ->shouldReceive('exists')->twice()->andReturn(false)
                ->getMock(),
            path: app('path.lang'),
            locale: 'pl', fallback: 'en',
            bundleOptions: ['strict' => true, 'useIsolating' => false],
        );

        $translator->get('missing.message');

        $translator->get('missing.message');
    }
}
