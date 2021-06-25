<?php

namespace Major\Fluent\Laravel\Tests;

use Illuminate\Contracts\Translation\Translator as TranslatorContract;
use Illuminate\Support\Facades\Lang;
use Illuminate\Translation\Translator as BaseTranslator;
use Major\Fluent\Laravel\FluentTranslator;

test('test environment is set up', function () {
    $path = app('path.lang');
    $locales = app('config')->getMany(['app.locale', 'app.fallback_locale']);

    expect($path)->toBe(__DIR__.'/lang');
    expect($locales)->toBe(['app.locale' => 'pl', 'app.fallback_locale' => 'en']);
});

test('fluent translator is properly registered in container', function ($abstract, $concrete) {
    expect(app($abstract))->toBeInstanceOf($concrete);
})->with([
    ['translator', FluentTranslator::class],
    [TranslatorContract::class, FluentTranslator::class],
    [FluentTranslator::class, FluentTranslator::class],
    [BaseTranslator::class, BaseTranslator::class],
]);

test('fluent translator can be resolved via dependency injection', function () {
    $translator = app(NeedsFluentTranslator::class)->translator;

    expect($translator)->toBeInstanceOf(FluentTranslator::class);
});

test('base translator can be resolved via dependency injection', function () {
    $translator = app(NeedsBaseTranslator::class)->translator;

    expect($translator)->toBeInstanceOf(BaseTranslator::class);
});

it('uses correct locales', function () {
    expect(trans()->getLocale())->toBe('pl')
        ->and(trans()->getFallback())->toBe('en');
});

test('locales can be changed', function () {
    app()->setLocale('de');
    app()->setFallbackLocale('pl');

    expect(trans()->getLocale())->toBe('de')
        ->and(trans()->getFallback())->toBe('pl');
});

it('works with Lang facade', function () {
    expect(Lang::get('test.test', ['var' => 'def']))->toBe('abc def');
});

it('works with __() helper', function () {
    expect(__('test.test', ['var' => 'def']))->toBe('abc def');
});

it('works with trans() helper', function () {
    expect(trans())->toBeInstanceOf(FluentTranslator::class);

    expect(trans('test.test', ['var' => 'def']))->toBe('abc def');
});

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
