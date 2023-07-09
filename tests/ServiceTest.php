<?php

namespace Major\Fluent\Laravel\Tests;

use Generator;
use Illuminate\Contracts\Translation\Translator as TranslatorContract;
use Illuminate\Support\Facades\Lang;
use Illuminate\Translation\Translator as BaseTranslator;
use Major\Fluent\Laravel\FluentTranslator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;

final class ServiceTest extends TestCase
{
    #[TestDox('test environment is properly set up')]
    public function testEnvironment(): void
    {
        $path = app('path.lang');
        $locales = app('config')->getMany(['app.locale', 'app.fallback_locale']);
        $fluent = app('config')->get('fluent');

        $this->assertSame(__DIR__ . '/lang', $path);

        $this->assertSame([
            'app.locale' => 'pl',
            'app.fallback_locale' => 'en',
        ], $locales);

        $this->assertSame([
            'strict' => true,
            'use_isolating' => false,
        ], $fluent);
    }

    #[DataProvider('provideProviderCases')]
    #[TestDox('fluent translator is properly registered in container')]
    public function testProvider($abstract, $concrete): void
    {
        $this->assertInstanceOf($concrete, app($abstract));
    }

    public static function provideProviderCases(): Generator
    {
        yield ['translator', FluentTranslator::class];

        yield [TranslatorContract::class, FluentTranslator::class];

        yield [FluentTranslator::class, FluentTranslator::class];

        yield [BaseTranslator::class, BaseTranslator::class];
    }

    #[TestDox('fluent translator can be resolved via dependency injection')]
    public function testFluentDI(): void
    {
        $translator = app(NeedsFluentTranslator::class)->translator;

        $this->assertInstanceOf(FluentTranslator::class, $translator);
    }

    #[TestDox('base translator can be resolved via dependency injection')]
    public function testBaseDI(): void
    {
        $translator = app(NeedsBaseTranslator::class)->translator;

        $this->assertInstanceOf(BaseTranslator::class, $translator);
    }

    #[TestDox('it uses correct locales')]
    public function testCorrectLocales(): void
    {
        $this->assertSame('pl', trans()->getLocale());
        $this->assertSame('en', trans()->getFallback());
    }

    #[TestDox('locales can be changed')]
    public function testChangeLocales(): void
    {
        app()->setLocale('de');
        app()->setFallbackLocale('pl');

        $this->assertSame('de', trans()->getLocale());
        $this->assertSame('pl', trans()->getFallback());
    }

    #[TestDox('it works with Lang facade')]
    public function testFacade(): void
    {
        $this->assertSame('abc def', Lang::get('test.test', ['var' => 'def']));
    }

    #[TestDox('it works with __() helper')]
    public function testHelper(): void
    {
        $this->assertSame('abc def', __('test.test', ['var' => 'def']));
    }

    #[TestDox('it works with trans() facade')]
    public function testTransHelper(): void
    {
        $this->assertInstanceOf(FluentTranslator::class, trans());

        $this->assertSame('abc def', trans('test.test', ['var' => 'def']));
    }
}

class NeedsFluentTranslator
{
    public function __construct(
        public FluentTranslator $translator,
    ) { }
}

class NeedsBaseTranslator
{
    public function __construct(
        public BaseTranslator $translator,
    ) { }
}
