<?php

namespace App\Services;

use App\Models\CafeModel;
use App\Models\CafeFeeTranslationModel;
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
        private readonly CafeFeeTranslationModel $feeTranslations = new CafeFeeTranslationModel(),
        private readonly CafeLanguageModel $cafeLanguages = new CafeLanguageModel(),
        private readonly CategoryModel $categories = new CategoryModel(),
        private readonly CategoryTranslationModel $categoryTranslations = new CategoryTranslationModel(),
        private readonly MenuItemModel $items = new MenuItemModel(),
        private readonly MenuItemTranslationModel $itemTranslations = new MenuItemTranslationModel(),
        private readonly LanguageCatalogService $languageCatalog = new LanguageCatalogService(),
        private readonly PublicUiTextCatalogService $uiTextCatalog = new PublicUiTextCatalogService(),
        private readonly ActivationService $activationService = new ActivationService(),
    ) {
    }

    public function buildByUsername(string $username): ?array
    {
        $cafe = $this->cafes->findByUsername($username);

        if ($cafe === null) {
            return null;
        }

        return $this->buildFromCafe($cafe);
    }

    public function buildByCode(string $code): ?array
    {
        $cafe = $this->cafes->findByCode($code);

        if ($cafe === null) {
            return null;
        }

        return $this->buildFromCafe($cafe);
    }

    private function buildFromCafe(array $cafe): array
    {
        $menuUpdatedAt = $cafe['menu_updated_at'] ?? $cafe['updated_at'] ?? date('Y-m-d H:i:s');
        $defaultLanguageCode = $this->languageCatalog->getDefaultCafeLanguageCode();

        $languages = $this->cafeLanguages->getByCafe((int) $cafe['id']);

        if ($languages === []) {
            $languages = [[
                'language_code' => $defaultLanguageCode,
                'sort_order'    => 1,
            ]];
        }

        $extraFeeEnabled = (bool) ($cafe['extra_fee_enabled'] ?? false);
        $defaultLanguage = menu_default_language($languages, $defaultLanguageCode);
        $publicStatus = (string) ($cafe['status'] ?? 'inactive');
        $hasPublicMenu = in_array($publicStatus, ['active', 'demo'], true);
        $categories = $hasPublicMenu ? $this->categories->getByCafe((int) $cafe['id'], true) : [];
        $items = $hasPublicMenu ? $this->items->getPublicItemsByCafe((int) $cafe['id']) : [];
        $languageCodes = array_map(static fn (array $language): string => $language['language_code'], $languages);
        $feeTranslations = $extraFeeEnabled && $hasPublicMenu
            ? $this->groupFeeTranslations($this->feeTranslations->getByCafeId((int) $cafe['id'], $languageCodes))
            : [];
        $categoryTranslations = $this->groupCategoryTranslations(
            $this->categoryTranslations->getByCategoryIds(array_map(static fn (array $category): int => (int) $category['id'], $categories), $languageCodes)
        );
        $itemTranslations = $this->groupItemTranslations(
            $this->itemTranslations->getByMenuItemIds(array_map(static fn (array $item): int => (int) $item['id'], $items), $languageCodes)
        );

        return [
            'meta'       => [
                'username'   => $cafe['username'],
                'updated_at' => Time::parse($menuUpdatedAt, app_timezone())->toDateTime()->format(DATE_ATOM),
                'default_language' => $defaultLanguage,
                'languages'  => array_values(array_filter(array_map(
                    fn (array $language): ?array => $this->languageCatalog->getLanguage($language['language_code']),
                    $languages,
                ))),
            ],
            'cafe'       => [
                'name'         => $cafe['cafe_name'],
                'status'       => $publicStatus,
                'slogan'       => $cafe['slogan'] ?? null,
                'logo_url'     => menu_asset_url($cafe['logo_path']),
                'currency'     => $cafe['currency_name'],
                'theme_style'  => $cafe['theme_style'],
                'address'      => $cafe['address_text'],
                'location_url' => $cafe['location_url'],
                'activation_url' => $this->activationService->getActivationUrl(),
                'extra_fee'    => [
                    'enabled'      => $extraFeeEnabled && $hasPublicMenu,
                    'type'         => $extraFeeEnabled && $hasPublicMenu ? ($cafe['extra_fee_type'] ?? null) : null,
                    'value'        => $extraFeeEnabled && $hasPublicMenu && $cafe['extra_fee_value'] !== null ? (float) $cafe['extra_fee_value'] : null,
                    'translations' => $feeTranslations,
                ],
            ],
            'public_status' => $publicStatus,
            'categories' => array_map(static fn (array $category): array => [
                'id'         => (int) $category['id'],
                'sort_order' => (int) $category['sort_order'],
                'icon_url'   => menu_asset_url($category['icon_path']),
                'translations' => $categoryTranslations[(int) $category['id']] ?? [],
            ], $categories),
            'ui_translations' => $this->uiTextCatalog->getTranslationsForLanguages($languageCodes),
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

    private function groupFeeTranslations(array $rows): array
    {
        $grouped = [];

        foreach ($rows as $row) {
            $grouped[$row['language_code']] = [
                'label' => $row['label'],
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
