<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\CategoryTranslationModel;
use App\Services\CafeLanguageService;
use App\Services\CafeService;
use App\Services\CategoryService;

class CategoryController extends BaseController
{
    public function __construct(
        private readonly CafeService $cafeService = new CafeService(),
        private readonly CategoryModel $categories = new CategoryModel(),
        private readonly CategoryTranslationModel $categoryTranslations = new CategoryTranslationModel(),
        private readonly CafeLanguageService $cafeLanguages = new CafeLanguageService(),
        private readonly CategoryService $categoryService = new CategoryService(),
    ) {
    }

    public function index(): string
    {
        $cafe = $this->cafeService->getCurrentCafe();

        return view('admin/categories/index', [
            'title'      => 'Категории',
            'cafe'       => $cafe,
            'categories' => $this->categories->getByCafe((int) $cafe['id']),
        ]);
    }

    public function new(): string
    {
        return view('admin/categories/form', [
            'title'        => 'Новая категория',
            'category'     => null,
            'translations' => [],
            'languages'    => $this->cafeLanguages->getByCafe((int) $this->cafeService->getCurrentCafeId()),
            'action'       => site_url('admin/categories'),
        ]);
    }

    public function create()
    {
        $cafeId = $this->cafeService->getCurrentCafeId();
        $data = $this->collectPayload($cafeId);

        if (! $this->categoryService->create($cafeId, $data)) {
            return redirect()->back()->withInput()->with('errors', $this->categoryService->getErrors());
        }

        $this->cafeService->touchMenuUpdatedAt($cafeId);

        return redirect()->to(site_url('admin/categories'))->with('success', 'Категория создана.');
    }

    public function edit(int $id): string
    {
        $category = $this->findOwnedCategoryOrFail($id);

        return view('admin/categories/form', [
            'title'        => 'Редактирование категории',
            'category'     => $category,
            'translations' => $this->categoryTranslations->getByCategoryId($id),
            'languages'    => $this->cafeLanguages->getByCafe((int) $category['cafe_id']),
            'action'       => site_url('admin/categories/' . $id),
        ]);
    }

    public function update(int $id)
    {
        $category = $this->findOwnedCategoryOrFail($id);
        $data = $this->collectPayload((int) $category['cafe_id']);

        if (! $this->categoryService->update($category, $data)) {
            return redirect()->back()->withInput()->with('errors', $this->categoryService->getErrors());
        }

        $this->cafeService->touchMenuUpdatedAt((int) $category['cafe_id']);

        return redirect()->to(site_url('admin/categories'))->with('success', 'Категория обновлена.');
    }

    public function delete(int $id)
    {
        $category = $this->findOwnedCategoryOrFail($id);
        $this->categories->delete($id);
        $this->cafeService->touchMenuUpdatedAt((int) $category['cafe_id']);

        return redirect()->to(site_url('admin/categories'))->with('success', 'Категория удалена.');
    }

    private function collectPayload(int $cafeId): array
    {
        return [
            'cafe_id'    => $cafeId,
            'sort_order' => (int) $this->request->getPost('sort_order'),
            'is_active'  => $this->request->getPost('is_active') ? 1 : 0,
            'translations' => $this->collectTranslations(),
        ];
    }

    private function collectTranslations(): array
    {
        $translations = [];

        foreach ((array) $this->request->getPost('translations') as $languageCode => $row) {
            $translations[(string) $languageCode] = [
                'name' => trim((string) ($row['name'] ?? '')),
            ];
        }

        return $translations;
    }

    private function findOwnedCategoryOrFail(int $id): array
    {
        $category = $this->categories->findByCafe((int) $this->cafeService->getCurrentCafeId(), $id);

        if ($category === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return $category;
    }
}
