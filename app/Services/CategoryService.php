<?php

namespace App\Services;

use App\Models\CategoryModel;
use Config\Database;

class CategoryService
{
    private array $errors = [];

    public function __construct(
        private readonly CategoryModel $categories = new CategoryModel(),
        private readonly CafeLanguageService $cafeLanguages = new CafeLanguageService(),
        private readonly CategoryTranslationService $translations = new CategoryTranslationService(),
    ) {
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function create(int $cafeId, array $payload): bool
    {
        $db = Database::connect();
        $db->transBegin();

        $baseData = [
            'cafe_id'    => $cafeId,
            'sort_order' => $payload['sort_order'],
            'is_active'  => $payload['is_active'],
            'icon_path'  => $payload['icon_path'] ?? null,
        ];

        if ($this->categories->insert($baseData) === false) {
            $db->transRollback();
            $this->errors = $this->categories->errors();

            return false;
        }

        $categoryId = (int) $this->categories->getInsertID();
        $languages = $this->cafeLanguages->getByCafe($cafeId);

        if (! $this->translations->syncForCategory($categoryId, $languages, $payload['translations'] ?? [])) {
            $db->transRollback();
            $this->errors = $this->translations->getErrors();

            return false;
        }

        $db->transCommit();
        $this->errors = [];

        return true;
    }

    public function update(array $category, array $payload): bool
    {
        $db = Database::connect();
        $db->transBegin();

        $baseData = [
            'cafe_id'    => (int) $category['cafe_id'],
            'sort_order' => $payload['sort_order'],
            'is_active'  => $payload['is_active'],
            'icon_path'  => $payload['icon_path'] ?? null,
        ];

        if ($this->categories->update((int) $category['id'], $baseData) === false) {
            $db->transRollback();
            $this->errors = $this->categories->errors();

            return false;
        }

        $languages = $this->cafeLanguages->getByCafe((int) $category['cafe_id']);

        if (! $this->translations->syncForCategory((int) $category['id'], $languages, $payload['translations'] ?? [])) {
            $db->transRollback();
            $this->errors = $this->translations->getErrors();

            return false;
        }

        $db->transCommit();
        $this->errors = [];

        return true;
    }
}
