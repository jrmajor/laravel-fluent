<?php

namespace Major\Fluent\Laravel\Tests;

use Generator;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Translation\Translator as BaseTranslator;
use Major\Fluent\Laravel\FluentTranslator;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;

final class LoaderTest extends TestCase
{
    #[TestDox('it tries to load a non-existent file only once')]
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

    #[DataProvider('provideUnsafePathCases')]
    #[TestDox('it does not load files from unsafe paths')]
    public function testUnsafePaths(string $key, string $locale): void
    {
        $translator = new FluentTranslator(
            baseTranslator: app(BaseTranslator::class),
            files: Mockery::mock(Filesystem::class)
                ->shouldNotReceive('exists')
                ->getMock(),
            path: app('path.lang'),
            locale: 'pl', fallback: 'en',
            bundleOptions: ['strict' => true, 'useIsolating' => false],
        );

        $this->assertSame($key, $translator->get($key, ['var' => 'x'], $locale, fallback: false));
    }

    public static function provideUnsafePathCases(): Generator
    {
        yield ['test.test', 'en/../pl'];

        yield ['test.test', '../lang/pl'];

        yield ['test.test', 'en\\..\\pl'];

        yield ['test.test', "pl\0"];

        yield ['test.test', ''];

        yield ['.test', 'pl'];

        yield ['a\\b.test', 'pl'];
    }
}
