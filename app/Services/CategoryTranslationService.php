<?php

namespace App\Services;

use App\Models\CategoryTranslationModel;

class CategoryTranslationService
{
    private array $errors = [];

    public function __construct(
        private readonly CategoryTranslationModel $translations = new CategoryTranslationModel(),
        private readonly AdminUiTextCatalogService $adminTexts = new AdminUiTextCatalogService(),
    ) {
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function syncForCategory(int $categoryId, array $cafeLanguages, array $payload): bool
    {
        $languageCodes = array_map(
            static fn (array $language): string => $language['language_code'] ?? $language['code'],
            $cafeLanguages,
        );

        $defaultLanguage = menu_default_language($cafeLanguages, menu_configured_default_language());
        $rows = [];
        $errors = [];

        foreach ($languageCodes as $code) {
            $name = trim((string) ($payload[$code]['name'] ?? ''));

            if ($code === $defaultLanguage && $name === '') {
                $errors['translations.' . $code . '.name'] = $this->adminTexts->translate('default_category_name_required');
                continue;
            }

            if ($code !== $defaultLanguage && $name === '') {
                continue;
            }

            $rows[] = [
                'category_id'   => $categoryId,
                'language_code' => $code,
                'name'          => $name,
            ];
        }

        $this->errors = $errors;

        if ($errors !== []) {
            return false;
        }

        $this->translations->where('category_id', $categoryId)->delete();

        if ($rows !== [] && $this->translations->insertBatch($rows) === false) {
            $this->errors = $this->translations->errors();

            return false;
        }

        return true;
    }
}
