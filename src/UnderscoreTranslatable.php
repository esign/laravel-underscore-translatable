<?php

namespace Esign\UnderscoreTranslatable;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

trait UnderscoreTranslatable
{
    public function isTranslatableAttribute(string $key): bool
    {
        return in_array($key, $this->getTranslatableAttributes());
    }

    public function getTranslatableAttributeName(string $key, ?string $locale = null): string
    {
        return $key . '_' . ($locale ?? App::getLocale());
    }

    public function getTranslation(string $key, ?string $locale = null, bool $useFallbackLocale = false): mixed
    {
        $value = $this->{$this->getTranslatableAttributeName($key, $locale)};

        if ($this->hasGetMutator($key)) {
            $value = $this->mutateAttribute($key, $value);
        }

        if (empty($value) && $useFallbackLocale) {
            $value = $this->getTranslation($key, config('app.fallback_locale', $locale), false);
        }

        return $value;
    }

    public function getTranslationWithFallback(string $key, ?string $locale = null): mixed
    {
        return $this->getTranslation($key, $locale, true);
    }

    public function getTranslationWithoutFallback(string $key, ?string $locale = null): mixed
    {
        return $this->getTranslation($key, $locale, false);
    }

    public function hasTranslation(string $key, ?string $locale = null): bool
    {
        return ! empty($this->getTranslationWithoutFallback($key, $locale));
    }

    public function setTranslation(string $key, string $locale, mixed $value): self
    {
        if ($this->hasSetMutator($key)) {
            $method = 'set' . Str::studly($key) . 'Attribute';
            $this->{$method}($value, $locale);
            $value = $this->attributes[$this->getTranslatableAttributeName($key, $locale)];
        }

        $this->{$this->getTranslatableAttributeName($key, $locale)} = $value;

        return $this;
    }

    public function setTranslations(string $key, array $translations): self
    {
        foreach ($translations as $locale => $translation) {
            $this->setTranslation($key, $locale, $translation);
        }

        return $this;
    }

    public function setAttribute($key, $value): mixed
    {
        if ($this->isTranslatableAttribute($key) && is_array($value)) {
            return $this->setTranslations($key, $value);
        }

        if ($this->isTranslatableAttribute($key)) {
            return $this->setTranslation($key, App::getLocale(), $value);
        }

        return parent::setAttribute($key, $value);
    }

    public function getTranslatableAttributes(): array
    {
        return $this->translatable ?? [];
    }

    public function getAttribute($key): mixed
    {
        if ($this->isTranslatableAttribute($key)) {
            return $this->getTranslation($key);
        }

        return parent::getAttribute($key);
    }
}
