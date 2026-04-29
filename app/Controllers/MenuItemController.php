<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\MenuItemModel;
use App\Models\MenuItemTranslationModel;
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
    ) {
    }

    public function index(): string
    {
        $cafeId = (int) $this->cafeService->getCurrentCafeId();

        return view('admin/menu_items/index', [
            'title' => 'Блюда меню',
            'items' => $this->items->getByCafe($cafeId),
        ]);
    }

    public function new(): string
    {
        $cafeId = (int) $this->cafeService->getCurrentCafeId();

        return view('admin/menu_items/form', [
            'title'        => 'Новое блюдо',
            'item'         => null,
            'translations' => [],
            'action'       => site_url('admin/menu-items'),
            'categories'   => $this->categories->getByCafe($cafeId),
            'languages'    => $this->cafeLanguages->getByCafe($cafeId),
        ]);
    }

    public function create()
    {
        return $this->persist();
    }

    public function edit(int $id): string
    {
        $cafeId = (int) $this->cafeService->getCurrentCafeId();
        $item = $this->items->findByCafe($cafeId, $id);

        if ($item === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        return view('admin/menu_items/form', [
            'title'        => 'Редактирование блюда',
            'item'         => $item,
            'translations' => $this->itemTranslations->getByMenuItemId($id),
            'action'       => site_url('admin/menu-items/' . $id),
            'categories'   => $this->categories->getByCafe($cafeId),
            'languages'    => $this->cafeLanguages->getByCafe($cafeId),
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
        $this->cafeService->bumpMenuVersion($cafeId);

        return redirect()->to(site_url('admin'))->with('success', 'Блюдо удалено.');
    }

    private function persist(?int $id = null)
    {
        $cafe = $this->cafeService->getCurrentCafe();
        $cafeId = (int) $cafe['id'];
        $currentItem = $id !== null ? $this->items->findByCafe($cafeId, $id) : null;
        $categoryId = $this->request->getPost('category_id');
        $categoryId = $categoryId !== '' ? (int) $categoryId : null;

        if ($categoryId !== null && $this->categories->findByCafe($cafeId, $categoryId) === null) {
            return redirect()->back()->withInput()->with('error', 'Выбранная категория не принадлежит вашему кафе.');
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

        $this->cafeService->bumpMenuVersion($cafeId);

        return redirect()->to(site_url('admin'))
            ->with('success', $id === null ? 'Блюдо создано.' : 'Блюдо обновлено.');
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
