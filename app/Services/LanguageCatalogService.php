<?php

namespace App\Services;

use RuntimeException;

class LanguageCatalogService
{
    private const DEFAULT_CAFE_LANGUAGE_CODE = 'en';

    /**
     * @return array<string, array{code: string, label: string, native_label: string, dir: string, flag: string}>
     */
    public function getSupportedLanguagesIndexed(): array
    {
        return [
            'en' => [
                'code'         => 'en',
                'label'        => 'English',
                'native_label' => 'English',
                'dir'          => 'ltr',
                'flag'         => '🇬🇧',
            ],
            'tr' => [
                'code'         => 'tr',
                'label'        => 'Turkish',
                'native_label' => 'Türkçe',
                'dir'          => 'ltr',
                'flag'         => '🇹🇷',
            ],
            'ru' => [
                'code'         => 'ru',
                'label'        => 'Russian',
                'native_label' => 'Русский',
                'dir'          => 'ltr',
                'flag'         => '🇷🇺',
            ],
            'uz' => [
                'code'         => 'uz',
                'label'        => 'Uzbek',
                'native_label' => 'O\'zbekcha',
                'dir'          => 'ltr',
                'flag'         => '🇺🇿',
            ],
            'ar' => [
                'code'         => 'ar',
                'label'        => 'Arabic',
                'native_label' => 'العربية',
                'dir'          => 'rtl',
                'flag'         => '🇸🇦',
            ],
            'zh' => [
                'code'         => 'zh',
                'label'        => 'Chinese',
                'native_label' => '中文',
                'dir'          => 'ltr',
                'flag'         => '🇨🇳',
            ],
            'ko' => [
                'code'         => 'ko',
                'label'        => 'Korean',
                'native_label' => '한국어',
                'dir'          => 'ltr',
                'flag'         => '🇰🇷',
            ],
            'ja' => [
                'code'         => 'ja',
                'label'        => 'Japanese',
                'native_label' => '日本語',
                'dir'          => 'ltr',
                'flag'         => '🇯🇵',
            ],
            'es' => [
                'code'         => 'es',
                'label'        => 'Spanish',
                'native_label' => 'Español',
                'dir'          => 'ltr',
                'flag'         => '🇪🇸',
            ],
            'de' => [
                'code'         => 'de',
                'label'        => 'German',
                'native_label' => 'Deutsch',
                'dir'          => 'ltr',
                'flag'         => '🇩🇪',
            ],
            'fa' => [
                'code'         => 'fa',
                'label'        => 'Farsi',
                'native_label' => 'فارسی',
                'dir'          => 'rtl',
                'flag'         => '🇮🇷',
            ],
            'fr' => [
                'code'         => 'fr',
                'label'        => 'French',
                'native_label' => 'Français',
                'dir'          => 'ltr',
                'flag'         => '🇫🇷',
            ],
            'pt' => [
                'code'         => 'pt',
                'label'        => 'Portuguese',
                'native_label' => 'Português',
                'dir'          => 'ltr',
                'flag'         => '🇵🇹',
            ],
            'it' => [
                'code'         => 'it',
                'label'        => 'Italian',
                'native_label' => 'Italiano',
                'dir'          => 'ltr',
                'flag'         => '🇮🇹',
            ],
            'hi' => [
                'code'         => 'hi',
                'label'        => 'Hindi',
                'native_label' => 'हिन्दी',
                'dir'          => 'ltr',
                'flag'         => '🇮🇳',
            ],
        ];
    }

    /**
     * @return list<array{code: string, label: string, native_label: string, dir: string, flag: string}>
     */
    public function getSupportedLanguages(): array
    {
        return array_values($this->getSupportedLanguagesIndexed());
    }

    public function getDefaultCafeLanguageCode(): string
    {
        $defaultLanguageCode = self::DEFAULT_CAFE_LANGUAGE_CODE;

        if (! array_key_exists($defaultLanguageCode, $this->getSupportedLanguagesIndexed())) {
            throw new RuntimeException('Configured default cafe language is not present in the supported language catalog.');
        }

        return $defaultLanguageCode;
    }

    public function getLanguage(string $code): ?array
    {
        $languages = $this->getSupportedLanguagesIndexed();

        return $languages[strtolower(trim($code))] ?? null;
    }

    public function isSupported(string $code): bool
    {
        return $this->getLanguage($code) !== null;
    }
}
