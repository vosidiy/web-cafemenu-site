<?php

namespace App\Services;

use App\Models\MenuItemModel;
use Config\Database;

class MenuItemService
{
    private array $errors = [];

    public function __construct(
        private readonly MenuItemModel $items = new MenuItemModel(),
        private readonly CafeLanguageService $cafeLanguages = new CafeLanguageService(),
        private readonly MenuItemTranslationService $translations = new MenuItemTranslationService(),
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

        $baseData = $this->extractBaseData($cafeId, $payload);

        if ($this->items->insert($baseData) === false) {
            $db->transRollback();
            $this->errors = $this->items->errors();

            return false;
        }

        $itemId = (int) $this->items->getInsertID();
        $languages = $this->cafeLanguages->getByCafe($cafeId);

        if (! $this->translations->syncForMenuItem($itemId, $languages, $payload['translations'] ?? [])) {
            $db->transRollback();
            $this->errors = $this->translations->getErrors();

            return false;
        }

        $db->transCommit();
        $this->errors = [];

        return true;
    }

    public function update(array $item, array $payload): bool
    {
        $db = Database::connect();
        $db->transBegin();

        $baseData = $this->extractBaseData((int) $item['cafe_id'], $payload);

        if ($this->items->update((int) $item['id'], $baseData) === false) {
            $db->transRollback();
            $this->errors = $this->items->errors();

            return false;
        }

        $languages = $this->cafeLanguages->getByCafe((int) $item['cafe_id']);

        if (! $this->translations->syncForMenuItem((int) $item['id'], $languages, $payload['translations'] ?? [])) {
            $db->transRollback();
            $this->errors = $this->translations->getErrors();

            return false;
        }

        $db->transCommit();
        $this->errors = [];

        return true;
    }

    private function extractBaseData(int $cafeId, array $payload): array
    {
        return [
            'cafe_id'       => $cafeId,
            'category_id'   => $payload['category_id'],
            'price'         => $payload['price'],
            'image_path'    => $payload['image_path'] ?? null,
            'is_available'  => $payload['is_available'],
            'sort_order'    => $payload['sort_order'],
        ];
    }
}
