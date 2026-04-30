<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<?php $priceCurrency = trim((string) ($currencyName ?? '')); ?>

<header class="d-flex align-items-center py-4">
    <a title="Назад" href="<?= site_url('admin') ?>" class="btn btn-neutral btn-icon"> 
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left-icon lucide-arrow-left"><path d="m12 19-7-7 7-7"/><path d="M19 12H5"/></svg>
    </a> <span class="px-2 text-muted"> /</span>
    <h2 class="text-3xl"><?= esc($title) ?></h2>
</header>

<div class="card">
    <div class="card-body">
        <form method="post" action="<?= esc($action) ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <?php $defaultLanguage = menu_default_language($languages, menu_configured_default_language()); ?>
            
            <?php foreach ($languages as $language): ?>
                <?php
                    $languageCode = $language['language_code'] ?? $language['code'];
                    $savedTranslation = $translations[$languageCode] ?? [];
                ?>
                <div style="border-left:4px solid var(--primary-300)" class="pl-3 mb-6">
                    <div class="mb-3">
                        <label class="form-label">
                            Название: <?= esc(($language['flag'] ?? '') . ' ' . $language['native_label']) ?>
                            <?php if ($languageCode === $defaultLanguage): ?>
                                <span class="text-danger">*</span>
                            <?php endif; ?>
                        </label>
                        <input  type="text" name="translations[<?= esc($languageCode) ?>][name]" class="form-control"
                            value="<?= esc(menu_old_translation($languageCode, 'name', $savedTranslation['name'] ?? '')) ?>"
                            <?= $languageCode === $defaultLanguage ? 'required' : '' ?>
                        >
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Описание: <?= esc(($language['flag'] ?? '') . ' ' . $language['native_label']) ?></label>
                        <textarea name="translations[<?= esc($languageCode) ?>][description]" class="form-control" rows="2"><?= esc(menu_old_translation($languageCode, 'description', $savedTranslation['description'] ?? '')) ?></textarea>
                    </div>
                </div>
            <?php endforeach; ?>
            <p class="my-3"><a href="<?= site_url('admin/settings') ?>#languages">⚙️ Настройка языки</a></p>
            <hr>

            <div class="row">
                <div class="md:col-3 mb-3">
                    <label class="form-label">
                        Цена <?= $priceCurrency !== '' ? ' (' . esc($priceCurrency) . ')' : '' ?>
                    </label>
                    <input type="number" step="0.01" min="0.01" name="price" class="form-control" value="<?= esc((string) menu_old('price', $item['price'] ?? '')) ?>" required> 
                </div>
                <div class="md:col-4 mb-3">
                    <label class="form-label">Категория</label>
                    <select name="category_id" class="form-select">
                        <option value="">Без категории</option>
                        <?php $selectedCategory = (string) menu_old('category_id', $item['category_id'] ?? ''); ?>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= esc($category['id']) ?>" <?= $selectedCategory === (string) $category['id'] ? 'selected' : '' ?>>
                                <?= esc($category['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="row">
            <div class="mb-4 md:col-3">
                <label class="form-label">Изображение</label>
                <input type="file" name="image_file" class="form-control" accept=".jpg,.jpeg,.png,.webp,.svg">
                <?php if (! empty($item['image_path'])): ?>
                    <div class="mt-2"><img src="<?= esc(menu_asset_url($item['image_path'])) ?>" alt="Preview" class="img-thumbnail" style="max-height: 90px;"></div>
                <?php endif; ?>
            </div>
            </div>

            <div class="row mb-5">
                <div class="md:col-3 mb-4">
                    <label class="form-label">Порядок сортировки</label>
                    <input type="number" name="sort_order" class="form-control" value="<?= esc((string) menu_old('sort_order', $item['sort_order'] ?? 0)) ?>" required>
                </div>
                <div class="md:col-3 d-flex  mb-4 align-items-end">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="is_available" id="isAvailable" value="1" <?= (int) menu_old('is_available', $item['is_available'] ?? 1) === 1 ? 'checked' : '' ?>>
                        <label class="form-check-label" for="isAvailable">Доступно</label>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Сохранить блюдо</button>
        </form>
    </div>
</div>
<br><br>

<?= $this->endSection() ?>
