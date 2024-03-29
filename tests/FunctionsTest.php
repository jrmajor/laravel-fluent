<?php

namespace Major\Fluent\Laravel\Tests;

use Major\Fluent\Exceptions\Bundle\FunctionExistsException;
use Major\Fluent\Exceptions\Resolver\ReferenceException;
use PHPUnit\Framework\Attributes\TestDox;

final class FunctionsTest extends TestCase
{
    #[TestDox('function can be used')]
    public function testUsage(): void
    {
        $this->translator->addFunction('CONCAT', fn (string ...$args) => implode('', $args));

        $this->assertSame('FooBar', __('functions.strings'));
    }

    #[TestDox('arguments can be passed to function')]
    public function testArguments(): void
    {
        $this->translator->addFunction('CONCAT', fn (string ...$args) => implode('', $args));

        $this->assertSame('BarBaz', __('functions.args', ['foo' => 'Bar', 'bar' => 'Baz']));
    }

    #[TestDox('function can not be registered twice')]
    public function testRegisterTwice(): void
    {
        $this->expectException(FunctionExistsException::class);
        $this->expectExceptionMessage('Attempt to override an existing function: CONCAT().');

        $this->translator->addFunction('CONCAT', fn (string ...$args) => implode('', $args));
        $this->translator->addFunction('CONCAT', fn (string ...$args) => implode(', ', $args));
    }

    #[TestDox('function can be registered after bundle is resolved')]
    public function testLateRegistration(): void
    {
        try {
            $this->assertSame('FooBar', __('functions.strings'));

            $this->fail('Exception should have been thrown.');
        } catch (ReferenceException $e) {
            $this->assertSame('Unknown function: CONCAT().', $e->getMessage());
        }

        $this->translator->addFunction('CONCAT', fn (string ...$args) => implode('', $args));

        $this->assertSame('FooBar', __('functions.strings'));
    }
}
