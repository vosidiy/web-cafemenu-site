<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\MenuItemModel;
use App\Models\MenuItemTranslationModel;
use App\Services\AdminUiTextCatalogService;
use App\Services\CafeLanguageService;
use App\Services\CafeService;
use App\Services\FileUploadService;
use App\Services\MenuItemService;
use CodeIgniter\Exceptions\PageNotFoundException;
use RuntimeException;

class MenuItemController extends BaseController
{
    public function __construct(
        private readonly CafeService $cafeService = new CafeService(),
        private readonly MenuItemModel $items = new MenuItemModel(),
        private readonly CategoryModel $categories = new CategoryModel(),
        private readonly MenuItemTranslationModel $itemTranslations = new MenuItemTranslationModel(),
        private readonly CafeLanguageService $cafeLanguages = new CafeLanguageService(),
        private readonly FileUploadService $uploads = new FileUploadService(),
        private readonly MenuItemService $itemService = new MenuItemService(),
        private readonly AdminUiTextCatalogService $adminTexts = new AdminUiTextCatalogService(),
    ) {
    }

    public function index(): string
    {
        $cafeId = (int) $this->cafeService->getCurrentCafeId();
        $items = $this->items->getByCafe($cafeId);
        $languages = $this->cafeLanguages->getByCafe($cafeId);
        $languageCodes = array_values(array_filter(array_map(
            static fn (array $language): string => (string) ($language['language_code'] ?? $language['code'] ?? ''),
            $languages,
        )));
        $translationRows = $this->itemTranslations->getByMenuItemIds(
            array_map(static fn (array $item): int => (int) $item['id'], $items),
            $languageCodes,
        );
        $translationsByItem = [];

        foreach ($translationRows as $translationRow) {
            $translationsByItem[(int) $translationRow['menu_item_id']][(string) $translationRow['language_code']] = $translationRow;
        }

        foreach ($items as &$item) {
            $orderedTranslations = [];
            $searchTerms = [];
            $itemTranslations = $translationsByItem[(int) $item['id']] ?? [];

            foreach ($languages as $language) {
                $languageCode = (string) ($language['language_code'] ?? $language['code'] ?? '');
                $translation = $itemTranslations[$languageCode] ?? null;

                if ($translation === null) {
                    continue;
                }

                $name = trim((string) ($translation['name'] ?? ''));
                $description = trim((string) ($translation['description'] ?? ''));

                if ($name === '' && $description === '') {
                    continue;
                }

                $orderedTranslations[] = [
                    'code'        => $languageCode,
                    'flag'        => (string) ($language['flag'] ?? '🏳️'),
                    'label'       => (string) ($language['label'] ?? strtoupper($languageCode)),
                    'name'        => $name,
                    'description' => $description,
                ];

                if ($name !== '') {
                    $searchTerms[] = $name;
                }

                if ($description !== '') {
                    $searchTerms[] = $description;
                }
            }

            $item['translations'] = $orderedTranslations;
            $item['search_name'] = implode(' ', $searchTerms);
        }
        unset($item);

        return view('admin/menu_items/index', [
            'title' => 'menu_items_page_title',
            'items' => $items,
        ]);
    }

    public function new(): string
    {
        $cafeId = (int) $this->cafeService->getCurrentCafeId();
        $cafe = $this->cafeService->getCurrentCafe();

        return view('admin/menu_items/form', [
            'title'        => 'new_menu_item',
            'item'         => null,
            'translations' => [],
            'action'       => site_url('admin/menu-items'),
            'categories'   => $this->categories->getByCafe($cafeId),
            'languages'    => $this->cafeLanguages->getByCafe($cafeId),
            'currencyName' => $cafe['currency_name'] ?? null,
        ]);
    }

    public function create()
    {
        return $this->persist();
    }

    public function edit(int $id): string
    {
        $cafeId = (int) $this->cafeService->getCurrentCafeId();
        $cafe = $this->cafeService->getCurrentCafe();
        $item = $this->items->findByCafe($cafeId, $id);

        if ($item === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        return view('admin/menu_items/form', [
            'title'        => 'edit_menu_item',
            'item'         => $item,
            'translations' => $this->itemTranslations->getByMenuItemId($id),
            'action'       => site_url('admin/menu-items/' . $id),
            'categories'   => $this->categories->getByCafe($cafeId),
            'languages'    => $this->cafeLanguages->getByCafe($cafeId),
            'currencyName' => $cafe['currency_name'] ?? null,
        ]);
    }

    public function update(int $id)
    {
        return $this->persist($id);
    }

    public function delete(int $id)
    {
        $cafeId = (int) $this->cafeService->getCurrentCafeId();
        $item = $this->items->findByCafe($cafeId, $id);

        if ($item === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $this->items->delete($id);
        $this->cafeService->touchMenuUpdatedAt($cafeId);

        return redirect()->to(site_url('admin'))->with('success', $this->adminTexts->translate('item_deleted'));
    }

    private function persist(?int $id = null)
    {
        $cafe = $this->cafeService->getCurrentCafe();
        $cafeId = (int) $cafe['id'];
        $currentItem = $id !== null ? $this->items->findByCafe($cafeId, $id) : null;
        $categoryId = $this->request->getPost('category_id');
        $categoryId = $categoryId !== '' ? (int) $categoryId : null;

        if ($categoryId !== null && $this->categories->findByCafe($cafeId, $categoryId) === null) {
            return redirect()->back()->withInput()->with('error', $this->adminTexts->translate('selected_category_not_owned'));
        }

        $data = [
            'category_id'   => $categoryId,
            'price'         => (string) $this->request->getPost('price'),
            'is_available'  => $this->request->getPost('is_available') ? 1 : 0,
            'sort_order'    => (int) $this->request->getPost('sort_order'),
            'translations'  => $this->collectTranslations(),
        ];

        try {
            $imagePath = $this->uploads->storeUploadedImage($this->request->getFile('image_file'), $cafe['username']);
        } catch (RuntimeException $exception) {
            return redirect()->back()->withInput()->with('error', $exception->getMessage());
        }

        if ($imagePath !== null) {
            $data['image_path'] = $imagePath;
        } elseif ($id === null) {
            $data['image_path'] = null;
        } elseif (! empty($currentItem['image_path'])) {
            $data['image_path'] = $currentItem['image_path'];
        }

        if ($id === null) {
            $saved = $this->itemService->create($cafeId, $data);
        } else {
            if ($currentItem === null) {
                throw PageNotFoundException::forPageNotFound();
            }

            if ($imagePath === null && ! empty($currentItem['image_path'])) {
                $data['image_path'] = $currentItem['image_path'];
            }

            $saved = $this->itemService->update($currentItem, $data);
        }

        if (! $saved) {
            return redirect()->back()->withInput()->with('errors', $this->itemService->getErrors());
        }

        $this->cafeService->touchMenuUpdatedAt($cafeId);

        return redirect()->to(site_url('admin'))
            ->with('success', $this->adminTexts->translate($id === null ? 'item_created' : 'item_updated'));
    }

    private function collectTranslations(): array
    {
        $translations = [];

        foreach ((array) $this->request->getPost('translations') as $languageCode => $row) {
            $translations[(string) $languageCode] = [
                'name'        => trim((string) ($row['name'] ?? '')),
                'description' => trim((string) ($row['description'] ?? '')),
            ];
        }

        return $translations;
    }
}
