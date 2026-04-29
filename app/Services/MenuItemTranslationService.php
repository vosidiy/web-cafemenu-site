<?php

namespace App\Services;

use App\Models\MenuItemTranslationModel;

class MenuItemTranslationService
{
    private array $errors = [];

    public function __construct(
        private readonly MenuItemTranslationModel $translations = new MenuItemTranslationModel(),
    ) {
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function syncForMenuItem(int $menuItemId, array $cafeLanguages, array $payload): bool
    {
        $languageCodes = array_map(
            static fn (array $language): string => $language['language_code'] ?? $language['code'],
            $cafeLanguages,
        );

        $defaultLanguage = menu_default_language($cafeLanguages, 'ru');
        $rows = [];
        $errors = [];

        foreach ($languageCodes as $code) {
            $name = trim((string) ($payload[$code]['name'] ?? ''));
            $description = trim((string) ($payload[$code]['description'] ?? ''));

            if ($code === $defaultLanguage && $name === '') {
                $errors['translations.' . $code . '.name'] = 'Название блюда на языке по умолчанию обязательно.';
                continue;
            }

            if ($code !== $defaultLanguage && $name === '' && $description !== '') {
                $errors['translations.' . $code . '.name'] = 'Укажите название блюда для заполненного перевода.';
                continue;
            }

            if ($code !== $defaultLanguage && $name === '' && $description === '') {
                continue;
            }

            $rows[] = [
                'menu_item_id'  => $menuItemId,
                'language_code' => $code,
                'name'          => $name,
                'description'   => $description !== '' ? $description : null,
            ];
        }

        $this->errors = $errors;

        if ($errors !== []) {
            return false;
        }

        $this->translations->where('menu_item_id', $menuItemId)->delete();

        if ($rows !== [] && $this->translations->insertBatch($rows) === false) {
            $this->errors = $this->translations->errors();

            return false;
        }

        return true;
    }
}
