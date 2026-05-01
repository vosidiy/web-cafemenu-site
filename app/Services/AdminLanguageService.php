<?php

namespace App\Services;

use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Session\Session;

class AdminLanguageService
{
    private const COOKIE_NAME = 'admin_language';
    private const SESSION_KEY = 'admin_language';
    private const COOKIE_TTL_SECONDS = 31536000;

    public function __construct(
        private readonly LanguageCatalogService $languageCatalog = new LanguageCatalogService(),
    ) {
    }

    public function getSupportedLanguages(): array
    {
        return $this->languageCatalog->getSupportedLanguages();
    }

    public function getDefaultLanguageCode(): string
    {
        return $this->languageCatalog->getDefaultCafeLanguageCode();
    }

    public function resolveCurrentLanguageCode(?IncomingRequest $request = null, ?Session $session = null): string
    {
        $request ??= service('request');
        $session ??= session();

        $sessionCode = $this->normalizeLanguageCode((string) $session->get(self::SESSION_KEY));

        if ($this->languageCatalog->isSupported($sessionCode)) {
            return $sessionCode;
        }

        $cookieCode = $this->normalizeLanguageCode((string) $request->getCookie(self::COOKIE_NAME));

        if ($this->languageCatalog->isSupported($cookieCode)) {
            $session->set(self::SESSION_KEY, $cookieCode);

            return $cookieCode;
        }

        foreach ($this->extractBrowserLanguageCodes($request) as $browserCode) {
            if (! $this->languageCatalog->isSupported($browserCode)) {
                continue;
            }

            $session->set(self::SESSION_KEY, $browserCode);

            return $browserCode;
        }

        $defaultCode = $this->getDefaultLanguageCode();
        $session->set(self::SESSION_KEY, $defaultCode);

        return $defaultCode;
    }

    public function resolveCurrentLanguage(?IncomingRequest $request = null, ?Session $session = null): array
    {
        $code = $this->resolveCurrentLanguageCode($request, $session);

        return $this->languageCatalog->getLanguage($code)
            ?? $this->languageCatalog->getLanguage($this->getDefaultLanguageCode())
            ?? [
                'code' => $this->getDefaultLanguageCode(),
                'label' => 'English',
                'native_label' => 'English',
                'dir' => 'ltr',
                'flag' => '🇬🇧',
                'locale' => 'en-GB',
            ];
    }

    public function persistLanguage(string $languageCode, ResponseInterface $response, ?Session $session = null): string
    {
        $session ??= session();

        $normalizedCode = $this->normalizeLanguageCode($languageCode);
        $resolvedCode = $this->languageCatalog->isSupported($normalizedCode)
            ? $normalizedCode
            : $this->getDefaultLanguageCode();

        $session->set(self::SESSION_KEY, $resolvedCode);
        $response->setCookie(self::COOKIE_NAME, $resolvedCode, self::COOKIE_TTL_SECONDS, '', '/', '', null, true, 'Lax');

        return $resolvedCode;
    }

    public function sanitizeRedirectTarget(string $redirectTo, string $fallback): string
    {
        $redirectTo = trim($redirectTo);

        if ($redirectTo === '' || str_starts_with($redirectTo, '//')) {
            return $fallback;
        }

        $parsed = parse_url($redirectTo);

        if ($parsed === false || isset($parsed['scheme']) || isset($parsed['host']) || isset($parsed['user']) || isset($parsed['pass'])) {
            return $fallback;
        }

        $path = ltrim((string) ($parsed['path'] ?? ''), '/');

        if ($path === '') {
            return $fallback;
        }

        $query = isset($parsed['query']) && $parsed['query'] !== ''
            ? '?' . $parsed['query']
            : '';

        return $path . $query;
    }

    private function normalizeLanguageCode(string $languageCode): string
    {
        return strtolower(trim($languageCode));
    }

    /**
     * @return list<string>
     */
    private function extractBrowserLanguageCodes(IncomingRequest $request): array
    {
        $header = trim($request->getHeaderLine('Accept-Language'));

        if ($header === '') {
            return [];
        }

        $codes = [];

        foreach (explode(',', $header) as $part) {
            $languageTag = trim(explode(';', $part)[0] ?? '');

            if ($languageTag === '') {
                continue;
            }

            $codes[] = strtolower(explode('-', $languageTag)[0] ?? '');
        }

        return array_values(array_filter($codes, static fn (string $code): bool => $code !== ''));
    }
}
