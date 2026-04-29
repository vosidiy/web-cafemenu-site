<?php

namespace App\Services;

use App\Models\CafeModel;
use App\Models\CafeLanguageModel;
use App\Models\CategoryModel;
use App\Models\CategoryTranslationModel;
use App\Models\MenuItemModel;
use App\Models\MenuItemTranslationModel;
use CodeIgniter\I18n\Time;

class MenuBuilderService
{
    public function __construct(
        private readonly CafeModel $cafes = new CafeModel(),
        private readonly CafeLanguageModel $cafeLanguages = new CafeLanguageModel(),
        private readonly CategoryModel $categories = new CategoryModel(),
        private readonly CategoryTranslationModel $categoryTranslations = new CategoryTranslationModel(),
        private readonly MenuItemModel $items = new MenuItemModel(),
        private readonly MenuItemTranslationModel $itemTranslations = new MenuItemTranslationModel(),
        private readonly LanguageCatalogService $languageCatalog = new LanguageCatalogService(),
    ) {
    }

    public function buildByUsername(string $username): ?array
    {
        $cafe = $this->cafes->findActiveByUsername($username);

        if ($cafe === null) {
            return null;
        }

        $menuVersion = array_key_exists('menu_version', $cafe) ? (int) $cafe['menu_version'] : 1;
        $menuUpdatedAt = $cafe['menu_updated_at'] ?? $cafe['updated_at'] ?? date('Y-m-d H:i:s');

        $languages = $this->cafeLanguages->getByCafe((int) $cafe['id']);

        if ($languages === []) {
            $languages = [[
                'language_code' => 'ru',
                'sort_order'    => 1,
            ]];
        }

        $defaultLanguage = menu_default_language($languages, 'ru');
        $categories = $this->categories->getByCafe((int) $cafe['id'], true);
        $items = $this->items->getPublicItemsByCafe((int) $cafe['id']);
        $languageCodes = array_map(static fn (array $language): string => $language['language_code'], $languages);
        $categoryTranslations = $this->groupCategoryTranslations(
            $this->categoryTranslations->getByCategoryIds(array_map(static fn (array $category): int => (int) $category['id'], $categories), $languageCodes)
        );
        $itemTranslations = $this->groupItemTranslations(
            $this->itemTranslations->getByMenuItemIds(array_map(static fn (array $item): int => (int) $item['id'], $items), $languageCodes)
        );

        return [
            'meta'       => [
                'username'   => $cafe['username'],
                'version'    => $menuVersion,
                'updated_at' => Time::parse($menuUpdatedAt, app_timezone())->toDateTime()->format(DATE_ATOM),
                'default_language' => $defaultLanguage,
                'languages'  => array_values(array_filter(array_map(
                    fn (array $language): ?array => $this->languageCatalog->getLanguage($language['language_code']),
                    $languages,
                ))),
            ],
            'cafe'       => [
                'name'         => $cafe['cafe_name'],
                'slogan'       => $cafe['slogan'] ?? null,
                'logo_url'     => menu_asset_url($cafe['logo_path']),
                'pwa_icon_url' => menu_asset_url($cafe['pwa_icon_path']),
                'currency'     => $cafe['currency_name'],
                'theme_style'  => $cafe['theme_style'],
                'address'      => $cafe['address_text'],
                'location_url' => $cafe['location_url'],
            ],
            'categories' => array_map(static fn (array $category): array => [
                'id'         => (int) $category['id'],
                'sort_order' => (int) $category['sort_order'],
                'translations' => $categoryTranslations[(int) $category['id']] ?? [],
            ], $categories),
            'items'      => array_map(static fn (array $item): array => [
                'id'           => (int) $item['id'],
                'category_id'  => $item['category_id'] !== null ? (int) $item['category_id'] : null,
                'price'        => (float) $item['price'],
                'image_url'    => menu_asset_url($item['image_path']),
                'is_available' => (bool) $item['is_available'],
                'sort_order'   => (int) $item['sort_order'],
                'translations' => $itemTranslations[(int) $item['id']] ?? [],
            ], $items),
        ];
    }

    private function groupCategoryTranslations(array $rows): array
    {
        $grouped = [];

        foreach ($rows as $row) {
            $grouped[(int) $row['category_id']][$row['language_code']] = [
                'name' => $row['name'],
            ];
        }

        return $grouped;
    }

    private function groupItemTranslations(array $rows): array
    {
        $grouped = [];

        foreach ($rows as $row) {
            $grouped[(int) $row['menu_item_id']][$row['language_code']] = [
                'name'        => $row['name'],
                'description' => $row['description'],
            ];
        }

        return $grouped;
    }
}
