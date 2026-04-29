<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-3xl mb-1"><?= esc($title) ?></h2>
            <a href="<?= site_url('admin') ?>" class="btn btn-outline-secondary">Назад</a>
        </div>
        <div class="card shadow-sm">
            <div class="card-body">
                <form method="post" action="<?= esc($action) ?>" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <?php $defaultLanguage = menu_default_language($languages, 'ru'); ?>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Цена</label>
                            <input type="number" step="0.01" min="0.01" name="price" class="form-control" value="<?= esc((string) menu_old('price', $item['price'] ?? '')) ?>" required>
                        </div>
                        <div class="col-md-8 mb-3">
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
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Порядок сортировки</label>
                            <input type="number" name="sort_order" class="form-control" value="<?= esc((string) menu_old('sort_order', $item['sort_order'] ?? 0)) ?>" required>
                        </div>
                        <div class="col-md-3 mb-3 d-flex align-items-end">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_available" id="isAvailable" value="1" <?= (int) menu_old('is_available', $item['is_available'] ?? 1) === 1 ? 'checked' : '' ?>>
                                <label class="form-check-label" for="isAvailable">Доступно</label>
                            </div>
                        </div>
                    </div>
                    <?php foreach ($languages as $language): ?>
                        <?php
                            $languageCode = $language['language_code'] ?? $language['code'];
                            $savedTranslation = $translations[$languageCode] ?? [];
                        ?>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    Название блюда: <?= esc(($language['flag'] ?? '') . ' ' . $language['native_label']) ?>
                                    <?php if ($languageCode === $defaultLanguage): ?>
                                        <span class="text-danger">*</span>
                                    <?php endif; ?>
                                </label>
                                <input
                                    type="text"
                                    name="translations[<?= esc($languageCode) ?>][name]"
                                    class="form-control"
                                    value="<?= esc(menu_old_translation($languageCode, 'name', $savedTranslation['name'] ?? '')) ?>"
                                    <?= $languageCode === $defaultLanguage ? 'required' : '' ?>
                                >
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Описание: <?= esc(($language['flag'] ?? '') . ' ' . $language['native_label']) ?></label>
                                <textarea name="translations[<?= esc($languageCode) ?>][description]" class="form-control" rows="3"><?= esc(menu_old_translation($languageCode, 'description', $savedTranslation['description'] ?? '')) ?></textarea>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <div class="mb-4">
                        <label class="form-label">Изображение</label>
                        <input type="file" name="image_file" class="form-control" accept=".jpg,.jpeg,.png,.webp,.svg">
                        <?php if (! empty($item['image_path'])): ?>
                            <div class="mt-2"><img src="<?= esc(menu_asset_url($item['image_path'])) ?>" alt="Preview" class="img-thumbnail" style="max-height: 90px;"></div>
                        <?php endif; ?>
                    </div>
                    <button type="submit" class="btn btn-primary">Сохранить блюдо</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
