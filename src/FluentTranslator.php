<?php

namespace Major\Fluent\Laravel;

use Countable;
use Illuminate\Contracts\Translation\Translator as TranslatorContract;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Translation\Translator as BaseTranslator;
use Major\Fluent\Bundle\FluentBundle;

final class FluentTranslator implements TranslatorContract
{
    /** @var array<string, array<string, FluentBundle>> */
    protected array $loaded;

    public function __construct(
        protected BaseTranslator $baseTranslator,
        protected Filesystem $files,
        protected string $path,
        protected string $locale,
        protected string $fallback,
        /** @var array<string, mixed> */
        protected array $bundleOptions,
    ) { }

    /**
     * @param string $key
     * @param array<string, mixed> $replace
     * @param ?string $locale
     * @return string|array<string, mixed>
     */
    public function get($key, array $replace = [], $locale = null, bool $fallback = true): string|array
    {
        $locale ??= $this->locale;

        $segments = explode('.', $key, limit: 2);

        if (str_contains($key, '::') || count($segments) !== 2) {
            return $this->baseTranslator->get(...func_get_args());
        }

        [$group, $item] = $segments;

        $message = $this->getBundle($locale, $group)?->message($item, $replace);

        if ($fallback && $this->fallback !== $locale) {
            $message ??= $this->getBundle($this->fallback, $group)?->message($item, $replace);
        }

        return $message ?? $this->baseTranslator->get(...func_get_args());
    }

    protected function getBundle(string $locale, string $group): ?FluentBundle
    {
        return $this->loaded[$locale][$group] ??= $this->loadFtl($locale, $group);
    }

    protected function loadFtl(string $locale, string $group): ?FluentBundle
    {
        if (! $this->files->exists($full = "{$this->path}/{$locale}/{$group}.ftl")) {
            return null;
        }

        return $this->loaded[$locale][$group]
            = (new FluentBundle($locale, ...$this->bundleOptions))
                ->addFtl($this->files->get($full));
    }

    /**
     * @param string $key
     * @param Countable|int|array<mixed, mixed> $number
     * @param array<string, mixed> $replace
     * @param ?string $locale
     * @return string
     */
    public function choice($key, $number, array $replace = [], $locale = null)
    {
        return $this->baseTranslator->choice(...func_get_args());
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
