<?php

it('can get translations from current locale bundle', function () {
    expect(__('fallbacks.in-current-locale-bundle'))->toBe('In PL bundle.');
});

it('falls back to fallback locale bundle', function () {
    expect(__('fallbacks.in-fallback-locale-bundle'))->toBe('In EN bundle.');
});

it('falls back to current locale PHP file', function () {
    expect(__('fallbacks.in-current-locale-php'))->toBe('In PL php file.');
});

it('falls back to fallback locale PHP file', function () {
    expect(__('fallbacks.in-fallback-locale-php'))->toBe('In EN php file.');
});

it('can get translations from current locale bundle for attributes', function () {
    expect(__('fallbacks.in-current-locale-bundle.attr'))->toBe('Attribute in PL bundle.');
});

it('falls back to fallback locale bundle for attributes', function () {
    expect(__('fallbacks.in-fallback-locale-bundle.attr'))->toBe('Attribute in EN bundle.');
});

it('falls back to current locale PHP file for attributes', function () {
    expect(__('fallbacks.in-current-locale-php-nested.key'))->toBe('Nested key in PL php file.');
});

it('falls back to fallback locale PHP file for attributes', function () {
    expect(__('fallbacks.in-fallback-locale-php-nested.key'))->toBe('Nested key in EN php file.');
});

test('variables work in PHP fallbacks', function () {
    expect(__('fallbacks.var-in-php-fallback', ['status' => 'works']))->toBe('It works.');
});

it('always uses PHP fallback for choice()', function () {
    expect(trans_choice('fallbacks.choice', 2, ['var' => 'test']))->toBe('2 test.');
});
