<?php

namespace Major\Fluent\Laravel;

use Closure;
use Countable;
use Illuminate\Contracts\Translation\Loader;
use Illuminate\Contracts\Translation\Translator as TranslatorContract;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Translation\MessageSelector;
use Illuminate\Translation\Translator as BaseTranslator;
use Major\Fluent\Bundle\FluentBundle;
use Major\Fluent\Exceptions\Bundle\FunctionExistsException;

final class FluentTranslator implements TranslatorContract
{
    /** @var array<string, array<string, FluentBundle|false>> */
    private array $loaded = [];

    /** @var array<string, Closure> */
    private array $functions = [];

    public function __construct(
        private BaseTranslator $baseTranslator,
        private Filesystem $files,
        private string $path,
        private string $locale,
        private string $fallback,
        /** @var array{strict: bool, useIsolating: bool} */
        private array $bundleOptions,
    ) { }

    public function hasForLocale(string $key, ?string $locale = null): bool
    {
        return $this->has($key, $locale, false);
    }

    public function has(string $key, ?string $locale = null, bool $fallback = true): bool
    {
        return $this->get($key, [], $locale, $fallback) !== $key;
    }

    /**
     * @param string $key
     * @param array<string, mixed> $replace
     * @param ?string $locale
     *
     * @return string|array<string, mixed>
     */
    public function get($key, array $replace = [], $locale = null, bool $fallback = true): string|array
    {
        $locale ??= $this->locale;

        $segments = explode('.', $key, limit: 2);

        if (str_contains($key, '::') || count($segments) !== 2) {
            /** @phpstan-ignore return.type */
            return $this->baseTranslator->get($key, $replace, $locale, $fallback);
        }

        [$group, $item] = $segments;

        $message = $this->getBundle($locale, $group)?->message($item, $replace);

        if ($fallback && $this->fallback !== $locale) {
            $message ??= $this->getBundle($this->fallback, $group)?->message($item, $replace);
        }

        /** @phpstan-ignore return.type */
        return $message ?? $this->baseTranslator->get($key, $replace, $locale, $fallback);
    }

    public function addFunction(string $name, Closure $function): void
    {
        if (array_key_exists($name, $this->functions)) {
            throw new FunctionExistsException($name);
        }

        $this->functions[$name] = $function;

        foreach ($this->loaded as $locale) {
            foreach ($locale as $bundle) {
                if ($bundle !== false) {
                    $bundle->addFunction($name, $function);
                }
            }
        }
    }

    private function getBundle(string $locale, string $group): ?FluentBundle
    {
        if (! isset($this->loaded[$locale][$group])) {
            $this->loaded[$locale][$group] = $this->loadFtl($locale, $group) ?? false;
        }

        return $this->loaded[$locale][$group] ?: null;
    }

    private function loadFtl(string $locale, string $group): ?FluentBundle
    {
        $path = "{$this->path}/{$locale}/{$group}.ftl";

        if (! $this->files->exists($path)) {
            return null;
        }

        return (new FluentBundle($locale, ...$this->bundleOptions))
            ->addFtl($this->files->get($path))
            ->addFunctions($this->functions);
    }

    /**
     * @param string $key
     * @param Countable|int|array<mixed, mixed> $number
     * @param array<string, mixed> $replace
     * @param ?string $locale
     */
    public function choice($key, $number, array $replace = [], $locale = null): string
    {
        return $this->baseTranslator->choice($key, $number, $replace, $locale);
    }

    /**
     * @param array<mixed, mixed> $lines
     */
    public function addLines(array $lines, string $locale, string $namespace = '*'): void
    {
        $this->baseTranslator->addLines($lines, $locale, $namespace);
    }

    public function load(string $namespace, string $group, string $locale): void
    {
        $this->baseTranslator->load($namespace, $group, $locale);
    }

    public function addNamespace(string $namespace, string $hint): void
    {
        $this->baseTranslator->addNamespace($namespace, $hint);
    }

    public function addJsonPath(string $path): void
    {
        $this->baseTranslator->addJsonPath($path);
    }

    /**
     * @return array<string>
     */
    public function parseKey(string $key): array
    {
        /** @phpstan-ignore return.type */
        return $this->baseTranslator->parseKey($key);
    }

    public function getSelector(): MessageSelector
    {
        return $this->baseTranslator->getSelector();
    }

    public function setSelector(MessageSelector $selector): void
    {
        $this->baseTranslator->setSelector($selector);
    }

    public function getLoader(): Loader
    {
        return $this->baseTranslator->getLoader();
    }

    public function locale(): string
    {
        return $this->getLocale();
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     */
    public function setLocale($locale): void
    {
        $this->locale = $locale;
        $this->baseTranslator->setLocale($locale);
    }

    public function getFallback(): string
    {
        return $this->fallback;
    }

    public function setFallback(string $locale): void
    {
        $this->fallback = $locale;
        $this->baseTranslator->setFallback($locale);
    }
}
