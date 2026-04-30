<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\CategoryTranslationModel;
use App\Services\CafeLanguageService;
use App\Services\CafeService;
use App\Services\CategoryService;
use App\Services\FileUploadService;
use CodeIgniter\Exceptions\PageNotFoundException;
use RuntimeException;

class CategoryController extends BaseController
{
    public function __construct(
        private readonly CafeService $cafeService = new CafeService(),
        private readonly CategoryModel $categories = new CategoryModel(),
        private readonly CategoryTranslationModel $categoryTranslations = new CategoryTranslationModel(),
        private readonly CafeLanguageService $cafeLanguages = new CafeLanguageService(),
        private readonly CategoryService $categoryService = new CategoryService(),
        private readonly FileUploadService $uploads = new FileUploadService(),
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
        return $this->persist();
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
        return $this->persist($id);
    }

    public function delete(int $id)
    {
        $category = $this->findOwnedCategoryOrFail($id);
        $this->categories->delete($id);
        $this->cafeService->touchMenuUpdatedAt((int) $category['cafe_id']);

        return redirect()->to(site_url('admin/categories'))->with('success', 'Категория удалена.');
    }

    private function persist(?int $id = null)
    {
        $cafe = $this->cafeService->getCurrentCafe();

        if ($cafe === null) {
            return redirect()->to(site_url('login'));
        }

        $cafeId = (int) $cafe['id'];
        $category = $id !== null ? $this->findOwnedCategoryOrFail($id) : null;
        $data = $this->collectPayload($cafeId);

        try {
            $iconPath = $this->storeCategoryIcon($cafe['username']);
        } catch (RuntimeException $exception) {
            return redirect()->back()->withInput()->with('error', $exception->getMessage());
        }

        if ($iconPath !== null) {
            $data['icon_path'] = $iconPath;
        } elseif ($category !== null && $this->request->getPost('remove_icon')) {
            $data['icon_path'] = null;
        } elseif ($category !== null && ! empty($category['icon_path'])) {
            $data['icon_path'] = $category['icon_path'];
        } else {
            $data['icon_path'] = null;
        }

        if ($category === null) {
            $saved = $this->categoryService->create($cafeId, $data);
        } else {
            $saved = $this->categoryService->update($category, $data);
        }

        if (! $saved) {
            return redirect()->back()->withInput()->with('errors', $this->categoryService->getErrors());
        }

        $this->cafeService->touchMenuUpdatedAt($cafeId);

        return redirect()->to(site_url('admin/categories'))
            ->with('success', $category === null ? 'Категория создана.' : 'Категория обновлена.');
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

    protected function storeCategoryIcon(string $username): ?string
    {
        $file = $this->request->getFile('icon_file');

        if ($file === null || ! $file->isValid() || $file->getError() === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        $mimeType = $file->getMimeType();

        if (! in_array($mimeType, ['image/png', 'image/svg+xml'], true)) {
            throw new RuntimeException('Please upload a valid PNG or SVG icon.');
        }

        return $this->uploads->storeUploadedImage($file, $username);
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
            throw PageNotFoundException::forPageNotFound();
        }

        return $category;
    }
}
