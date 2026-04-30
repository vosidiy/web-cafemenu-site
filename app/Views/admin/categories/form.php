<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-3xl mb-1"><?= esc($title) ?></h2>
            <a href="<?= site_url('admin/categories') ?>" class="btn btn-outline-secondary">Назад</a>
        </div>
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="post" action="<?= esc($action) ?>" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <?php $defaultLanguage = menu_default_language($languages, menu_configured_default_language()); ?>
                    <?php foreach ($languages as $language): ?>
                        <?php
                            $languageCode = $language['language_code'] ?? $language['code'];
                            $savedTranslation = $translations[$languageCode] ?? [];
                        ?>
                        <div class="mb-3">
                            <label class="form-label">
                                Название категории: <?= esc(($language['flag'] ?? '') . ' ' . $language['native_label']) ?>
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
                    <?php endforeach; ?>
                    <div class="mb-3">
                        <label class="form-label">Порядок сортировки</label>
                        <input type="number" name="sort_order" class="form-control" value="<?= esc((string) menu_old('sort_order', $category['sort_order'] ?? 0)) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Иконка категории</label>
                        <input type="file" name="icon_file" class="form-control" accept=".png,.svg">
                        <div class="form-text">Необязательно. Поддерживаются PNG и SVG.</div>
                        <?php if (! empty($category['icon_path'])): ?>
                            <div class="mt-2 d-flex align-items-center gap-3">
                                <img src="<?= esc(menu_asset_url($category['icon_path'])) ?>" alt="Иконка категории" class="img-thumbnail" style="max-height: 72px; max-width: 72px;">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remove_icon" id="removeIcon" value="1">
                                    <label class="form-check-label" for="removeIcon">Удалить текущую иконку</label>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1" <?= (int) menu_old('is_active', $category['is_active'] ?? 1) === 1 ? 'checked' : '' ?>>
                        <label class="form-check-label" for="isActive">Активна</label>
                    </div>
                    <button type="submit" class="btn btn-primary">Сохранить категорию</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
