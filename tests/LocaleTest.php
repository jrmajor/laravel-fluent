<?php

namespace Major\Fluent\Laravel\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\TestDox;

final class LocaleTest extends TestCase
{
    #[TestDox('it keeps the current locale when an invalid one is set')]
    public function testInvalidLocale(): void
    {
        try {
            $this->translator->setLocale('en/../pl');
            $this->fail('Expected an exception.');
        } catch (InvalidArgumentException) {
        }

        $this->assertSame('pl', $this->translator->getLocale());
    }
}
