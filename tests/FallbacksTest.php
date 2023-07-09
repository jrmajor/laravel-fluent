<?php

namespace Major\Fluent\Laravel\Tests;

use PHPUnit\Framework\Attributes\TestDox;

final class FallbacksTest extends TestCase
{
    #[TestDox('it can get translations from current locale bundle')]
    public function testCurrent(): void
    {
        $this->assertSame(
            'In PL bundle.',
            __('fallbacks.in-current-locale-bundle'),
        );
    }

    #[TestDox('it falls back to fallback locale bundle')]
    public function testFallback(): void
    {
        $this->assertSame(
            'In EN bundle.',
            __('fallbacks.in-fallback-locale-bundle'),
        );
    }

    #[TestDox('it falls back to current locale PHP file')]
    public function testPHPCurrent(): void
    {
        $this->assertSame(
            'In PL php file.',
            __('fallbacks.in-current-locale-php'),
        );
    }

    #[TestDox('it falls back to fallback locale PHP file')]
    public function testPHPFallback(): void
    {
        $this->assertSame(
            'In EN php file.',
            __('fallbacks.in-fallback-locale-php'),
        );
    }

    #[TestDox('it can get translations from current locale bundle for attributes')]
    public function testCurrentAttributes(): void
    {
        $this->assertSame(
            'Attribute in PL bundle.',
            __('fallbacks.in-current-locale-bundle.attr'),
        );
    }

    #[TestDox('it falls back to fallback locale bundle for attributes')]
    public function testFallbackAttributes(): void
    {
        $this->assertSame(
            'Attribute in EN bundle.',
            __('fallbacks.in-fallback-locale-bundle.attr'),
        );
    }

    #[TestDox('it falls back to current locale PHP file for attributes')]
    public function testPHPCurrentAttributes(): void
    {
        $this->assertSame(
            'Nested key in PL php file.',
            __('fallbacks.in-current-locale-php-nested.key'),
        );
    }

    #[TestDox('it falls back to fallback locale PHP file for attributes')]
    public function testPHPFallbackAttributes(): void
    {
        $this->assertSame(
            'Nested key in EN php file.',
            __('fallbacks.in-fallback-locale-php-nested.key'),
        );
    }

    #[TestDox('variables work in PHP fallbacks')]
    public function testVariablesInFallbacks(): void
    {
        $this->assertSame(
            'It works.',
            __('fallbacks.var-in-php-fallback', ['status' => 'works']),
        );
    }

    #[TestDox('it always uses PHP fallback for trans_choice()')]
    public function testTransChoiceFallback(): void
    {
        $this->assertSame(
            '2 test.',
            trans_choice('fallbacks.choice', 2, ['var' => 'test']),
        );
    }

    #[TestDox('it always uses PHP fallback for Translator::choice()')]
    public function testTranslatorChoiceFallback(): void
    {
        $this->assertSame(
            '2 test.',
            $this->translator->choice('fallbacks.choice', 2, ['var' => 'test']),
        );
    }
}
