<?php

namespace Major\Fluent\Laravel\Tests;

use Illuminate\Contracts\Translation\Translator as TranslatorContract;
use Illuminate\Translation\Translator as BaseTranslator;
use Major\Fluent\Laravel\FluentTranslator;

test('test environment is set up', function () {
    expect(app('path.lang'))->toBe(__DIR__.'/lang');

    expect(app('config')->getMany(['app.locale', 'app.fallback_locale']))
        ->toBe(['app.locale' => 'pl', 'app.fallback_locale' => 'en']);
});

test('fluent translator is registered', function (string $abstract) {
    expect(app($abstract))->toBeInstanceOf(FluentTranslator::class);
})->with([
    'translator', TranslatorContract::class,
    BaseTranslator::class, FluentTranslator::class,
]);

it('sets correct locales', function () {
    config([
        'app.locale' => 'de',
        'app.fallback_locale' => 'pl',
    ]);

    expect(trans()->getLocale())->toBe('de')
        ->and(trans()->getFallback())->toBe('pl');
});

it('works with __() helper', function () {
    expect(__('test.test', ['var' => 'def']))->toBe('abc def');
});

it('works with trans() helper', function () {
    expect(trans())->toBeInstanceOf(FluentTranslator::class);

    expect(trans('test.test', ['var' => 'def']))->toBe('abc def');
});
