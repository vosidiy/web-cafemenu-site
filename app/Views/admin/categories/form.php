<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<header class="d-flex align-items-center py-4">
    <a title="Назад" href="<?= site_url('admin/categories') ?>" class="btn btn-neutral btn-icon"> 
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
                <div class="mb-4">
                    <label class="form-label">
                        Название: <?= esc(($language['flag'] ?? '') . ' ' . $language['native_label']) ?>
                        <?php if ($languageCode === $defaultLanguage): ?>
                            <span class="text-red">*</span>
                        <?php endif; ?>
                    </label>
                    <input
                        type="text"
                        name="translations[<?= esc($languageCode) ?>][name]"
                        class="form-control" placeholder="Название категории"
                        value="<?= esc(menu_old_translation($languageCode, 'name', $savedTranslation['name'] ?? '')) ?>"
                        <?= $languageCode === $defaultLanguage ? 'required' : '' ?>
                    >
                </div>
            <?php endforeach; ?>
            <hr>
            <div class="row">
                <div class="md:col-3 mb-4">
                    <label class="form-label">Иконка категории</label>
                    <input type="file" name="icon_file" class="form-control" accept=".png,.svg">
                    <div class="text-sm text-secondary mt-2">Необязательно. PNG, SVG.</div>
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
                <div class="md:col-3 mb-4">
                    <label class="form-label">Порядок сортировки</label>
                    <input type="number" name="sort_order" class="form-control" value="<?= esc((string) menu_old('sort_order', $category['sort_order'] ?? 0)) ?>" required>
                </div>
            </div>
            <div class="form-check mb-6">
                <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1" <?= (int) menu_old('is_active', $category['is_active'] ?? 1) === 1 ? 'checked' : '' ?>>
                <label class="form-check-label" for="isActive">Активна</label>
            </div>
            <button type="submit" class="btn btn-primary">Сохранить категорию</button>
        </form>
    </div>
</div>
<br><br>
<?= $this->endSection() ?>
