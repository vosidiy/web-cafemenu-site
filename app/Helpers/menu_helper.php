<?php

use App\Services\AdminLanguageService;
use App\Services\AdminUiTextCatalogService;
use App\Services\LanguageCatalogService;

if (! function_exists('menu_asset_url')) {
    function menu_asset_url(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        return base_url(ltrim($path, '/'));
    }
}

if (! function_exists('menu_old')) {
    function menu_old(string $key, mixed $default = ''): mixed
    {
        return old($key) ?? $default;
    }
}

if (! function_exists('menu_old_translation')) {
    function menu_old_translation(string $languageCode, string $field, mixed $default = ''): mixed
    {
        return old('translations.' . $languageCode . '.' . $field) ?? $default;
    }
}

if (! function_exists('menu_old_fee_translation')) {
    function menu_old_fee_translation(string $languageCode, string $field, mixed $default = ''): mixed
    {
        return old('fee_translations.' . $languageCode . '.' . $field) ?? $default;
    }
}

if (! function_exists('menu_supported_languages')) {
    function menu_supported_languages(): array
    {
        return (new LanguageCatalogService())->getSupportedLanguages();
    }
}

if (! function_exists('menu_supported_language_codes')) {
    function menu_supported_language_codes(): array
    {
        return array_map(
            static fn (array $language): string => $language['code'],
            menu_supported_languages(),
        );
    }
}

if (! function_exists('menu_configured_default_language')) {
    function menu_configured_default_language(): string
    {
        return (new LanguageCatalogService())->getDefaultCafeLanguageCode();
    }
}

if (! function_exists('menu_default_language')) {
    function menu_default_language(array $languages, ?string $fallback = null): string
    {
        $fallback ??= menu_configured_default_language();

        foreach ($languages as $language) {
            if ((int) ($language['sort_order'] ?? 0) === 1) {
                return (string) ($language['language_code'] ?? $language['code'] ?? $fallback);
            }
        }

        if ($languages !== []) {
            return (string) ($languages[0]['language_code'] ?? $languages[0]['code'] ?? $fallback);
        }

        return $fallback;
    }
}

if (! function_exists('menu_language_direction')) {
    function menu_language_direction(string $languageCode): string
    {
        $language = (new LanguageCatalogService())->getLanguage($languageCode);

        return $language['dir'] ?? 'ltr';
    }
}

if (! function_exists('menu_translation_value')) {
    function menu_translation_value(array $translations, string $languageCode, string $defaultLanguage, string $field): ?string
    {
        $selected = $translations[$languageCode][$field] ?? null;

        if (is_string($selected) && trim($selected) !== '') {
            return $selected;
        }

        $fallback = $translations[$defaultLanguage][$field] ?? null;

        if (is_string($fallback) && trim($fallback) !== '') {
            return $fallback;
        }

        return null;
    }
}

if (! function_exists('admin_ui')) {
    function admin_ui(string $key, array $replacements = []): string
    {
        return (new AdminUiTextCatalogService())->translate($key, null, $replacements);
    }
}

if (! function_exists('admin_ui_current_language')) {
    function admin_ui_current_language(): array
    {
        return (new AdminLanguageService())->resolveCurrentLanguage();
    }
}

if (! function_exists('admin_ui_supported_languages')) {
    function admin_ui_supported_languages(): array
    {
        return (new AdminLanguageService())->getSupportedLanguages();
    }
}
