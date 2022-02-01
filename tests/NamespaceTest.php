<?php

namespace Major\Fluent\Laravel\Tests;

final class NamespaceTest extends TestCase
{
    /**
     * @testdox it can get namespaced translations
     */
    public function testNamespace(): void
    {
        $this->app['translator']->addNamespace('namespace', __DIR__.'/namespace');

        $this->assertSame(
            'In namespaced PL bundle.',
            __('namespace::test.in-namespaced-locale-bundle'),
        );
    }

    /**
     * @testdox it allows namespaced translations to be overridden
     */
    public function testOverride(): void
    {
        $this->app['translator']->addNamespace('override', __DIR__.'/namespace');

        $this->assertSame(
            'Override of namespaced PL bundle.',
            __('override::test.in-namespaced-locale-bundle'),
        );
    }
}
