<?php

namespace Major\Fluent\Laravel\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\TestDox;

final class TypedAccessorsTest extends TestCase
{
    #[TestDox('string() returns Fluent messages')]
    public function testStringFluent(): void
    {
        $this->assertSame('abc def', $this->translator->string('test.test', ['var' => 'def']));
    }

    #[TestDox('string() falls back to PHP files')]
    public function testStringPHP(): void
    {
        $this->assertSame('In PL php file.', $this->translator->string('fallbacks.in-current-locale-php'));
    }

    #[TestDox('string() throws for arrays')]
    public function testStringThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Translation value for key [fallbacks.in-current-locale-php-nested] must be a string, array given.');

        $this->translator->string('fallbacks.in-current-locale-php-nested');
    }

    #[TestDox('array() returns arrays from PHP files')]
    public function testArray(): void
    {
        $this->assertSame(
            ['key' => 'Nested key in PL php file.'],
            $this->translator->array('fallbacks.in-current-locale-php-nested'),
        );
    }

    #[TestDox('array() throws for Fluent messages')]
    public function testArrayThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Translation value for key [fallbacks.in-current-locale-bundle] must be an array, string given.');

        $this->translator->array('fallbacks.in-current-locale-bundle');
    }
}
