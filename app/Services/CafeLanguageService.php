<?php

namespace App\Services;

use App\Models\CafeLanguageModel;
use Config\Database;

class CafeLanguageService
{
    private array $errors = [];

    public function __construct(
        private readonly CafeLanguageModel $cafeLanguages = new CafeLanguageModel(),
        private readonly LanguageCatalogService $languageCatalog = new LanguageCatalogService(),
    ) {
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getSupportedLanguages(): array
    {
        return $this->languageCatalog->getSupportedLanguages();
    }

    public function getByCafe(int $cafeId): array
    {
        $rows = $this->cafeLanguages->getByCafe($cafeId);
        $defaultLanguageCode = $this->languageCatalog->getDefaultCafeLanguageCode();

        if ($rows === []) {
            return [$this->decorateLanguageRow([
                'cafe_id'       => $cafeId,
                'language_code' => $defaultLanguageCode,
                'sort_order'    => 1,
            ])];
        }

        return array_map(fn (array $row): array => $this->decorateLanguageRow($row), $rows);
    }

    public function getLanguageCodesByCafe(int $cafeId): array
    {
        return array_map(
            static fn (array $row): string => $row['language_code'],
            $this->getByCafe($cafeId),
        );
    }

    public function getDefaultLanguageCodeByCafe(int $cafeId): string
    {
        $languages = $this->getByCafe($cafeId);

        return menu_default_language($languages, $this->languageCatalog->getDefaultCafeLanguageCode());
    }

    public function syncForCafe(int $cafeId, array $selectedLanguages): bool
    {
        $codes = $this->normalizeLanguageCodes($selectedLanguages);

        if (! $this->validateSelection($codes)) {
            return false;
        }

        $db = Database::connect();

        $this->cafeLanguages->where('cafe_id', $cafeId)->delete();

        $rows = [];

        foreach ($codes as $index => $code) {
            $rows[] = [
                'cafe_id'       => $cafeId,
                'language_code' => $code,
                'sort_order'    => $index + 1,
            ];
        }

        if ($rows !== [] && $this->cafeLanguages->insertBatch($rows) === false) {
            $this->errors = $this->cafeLanguages->errors();

            return false;
        }

        if ($db->error()['code'] !== 0) {
            $this->errors = ['languages' => 'Не удалось сохранить языки кафе.'];

            return false;
        }

        $this->errors = [];

        return true;
    }

    public function normalizeLanguageCodes(array $selectedLanguages): array
    {
        $codes = [];

        foreach ($selectedLanguages as $languageCode) {
            $normalized = strtolower(trim((string) $languageCode));

            if ($normalized === '') {
                continue;
            }

            $codes[] = $normalized;
        }

        return array_values($codes);
    }

    private function validateSelection(array $codes): bool
    {
        $errors = [];

        if ($codes === []) {
            $errors['languages.0'] = 'Язык по умолчанию обязателен.';
        }

        if (count($codes) > 3) {
            $errors['languages'] = 'Можно выбрать максимум 3 языка.';
        }

        if (count(array_unique($codes)) !== count($codes)) {
            $errors['languages_unique'] = 'Языки не должны повторяться.';
        }

        foreach ($codes as $index => $code) {
            if (! $this->languageCatalog->isSupported($code)) {
                $errors['languages.' . $index] = 'Выбран неподдерживаемый язык.';
            }
        }

        $this->errors = $errors;

        return $errors === [];
    }

    private function decorateLanguageRow(array $row): array
    {
        $catalogLanguage = $this->languageCatalog->getLanguage($row['language_code']) ?? [
            'code'         => $row['language_code'],
            'label'        => strtoupper($row['language_code']),
            'native_label' => strtoupper($row['language_code']),
            'dir'          => 'ltr',
            'flag'         => '🏳️',
        ];

        return $row + $catalogLanguage;
    }
}
