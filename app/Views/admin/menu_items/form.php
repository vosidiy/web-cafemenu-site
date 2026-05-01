<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<?php $priceCurrency = trim((string) ($currencyName ?? '')); ?>

<header class="d-flex align-items-center py-4">
    <a title="<?= esc(admin_ui('go_back')) ?>" href="<?= site_url('admin') ?>" class="btn btn-neutral btn-icon">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left-icon lucide-arrow-left"><path d="m12 19-7-7 7-7"/><path d="M19 12H5"/></svg>
    </a> <span class="px-2 text-muted"> /</span>
    <h2 class="text-3xl"><?= esc(admin_ui($title)) ?></h2>
</header>

<div class="card">
    <div class="card-body">
        <form id="menuItemForm" method="post" action="<?= esc($action) ?>" enctype="multipart/form-data">
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
                            <?= esc(admin_ui('item_name_label')) ?>: <?= esc(($language['flag'] ?? '') . ' ' . $language['native_label']) ?>
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
                        <label class="form-label"><?= esc(admin_ui('description_label')) ?>: <?= esc(($language['flag'] ?? '') . ' ' . $language['native_label']) ?></label>
                        <textarea name="translations[<?= esc($languageCode) ?>][description]" class="form-control" rows="2"><?= esc(menu_old_translation($languageCode, 'description', $savedTranslation['description'] ?? '')) ?></textarea>
                    </div>
                </div>
            <?php endforeach; ?>
            <p class="my-3"><a href="<?= site_url('admin/settings') ?>#languages">⚙️ <?= esc(admin_ui('language_settings')) ?></a></p>
            <hr>

            <div class="row">
                <div class="md:col-3 mb-3">
                    <label class="form-label">
                        <?= esc(admin_ui('price_label')) ?><?= $priceCurrency !== '' ? ' (' . esc($priceCurrency) . ')' : '' ?>
                    </label>
                    <input type="number" step="0.01" min="0.01" name="price" class="form-control" value="<?= esc((string) menu_old('price', $item['price'] ?? '')) ?>" required> 
                </div>
                <div class="md:col-4 mb-3">
                    <p class="form-label"><?= esc(admin_ui('category_label')) ?></p>
                    <select name="category_id" class="form-select">
                        <option value=""><?= esc(admin_ui('uncategorized')) ?></option>
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
	                <div id="menuItemImageField" class="menu-item-image-field">
	                <label class="form-label"><?= esc(admin_ui('image_label')) ?></label>
                    <div id="menuItemImageDropzone" class="menu-item-image-picker"
                    >
                        <div class="card p-4 relative">
                            <input type="file"
                                id="menuItemImageFile"
                                name="image_file"
                                class="picker-area"
                                accept=".jpg,.jpeg,.png,.webp"
                            >
                            <div class="p-5 text-center">
                                <?= esc(admin_ui('image_drop_prompt')) ?>
                                <span class="menu-item-image-browse"><?= esc(admin_ui('image_browse_action')) ?></span>
                            </div>
                        </div>
                    </div>
		                <div class="text-sm text-secondary">JPG, PNG, WEBP</div>
		                <div id="menuItemImageStatus" class="menu-item-image-status is-hidden" aria-live="polite"></div>
                    <div id="menuItemImagePreviewShell" class="menu-item-image-preview-shell is-hidden">
                        <div class="menu-item-image-preview-frame">
                            <img id="menuItemImagePreview" alt="<?= esc(admin_ui('preview_alt')) ?>">
                        </div>
                        <div class="mt-2">
                            <button type="button" id="menuItemImageRemove"><?= esc(admin_ui('image_remove_action')) ?></button>
                        </div>
                    </div>
		                <?php if (! empty($item['image_path'])): ?>
		                    <div id="menuItemCurrentImage" class="menu-item-current-image mt-2">
	                        <img src="<?= esc(menu_asset_url($item['image_path'])) ?>" alt="<?= esc(admin_ui('preview_alt')) ?>" class="img-thumbnail" style="max-height: 90px;">
	                    </div>
	                <?php endif; ?>
                    </div>
	            </div>
	            </div>

            <div class="row mb-5">
                <div class="md:col-3 mb-4">
                    <label class="form-label"><?= esc(admin_ui('sort_order')) ?></label>
                    <input type="number" name="sort_order" class="form-control" value="<?= esc((string) menu_old('sort_order', $item['sort_order'] ?? 0)) ?>" required>
                </div>
                <div class="md:col-3 d-flex  mb-4 align-items-end">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="is_available" id="isAvailable" value="1" <?= (int) menu_old('is_available', $item['is_available'] ?? 1) === 1 ? 'checked' : '' ?>>
                        <label class="form-check-label" for="isAvailable"><?= esc(admin_ui('available')) ?></label>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary"><?= esc(admin_ui('save_menu_item')) ?></button>
        </form>
    </div>
	</div>
	<br><br>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= esc(base_url('plugins/compressor.min.js')) ?>"></script>
<script src="<?= esc(base_url('admin-menu-item-image-uploader.js')) ?>"></script>
<script>
			    window.initMenuItemImageUploader?.({
			        inputId: 'menuItemImageFile',
		            fieldSelector: '#menuItemImageField',
			        currentImageSelector: '#menuItemCurrentImage',
			        statusSelector: '#menuItemImageStatus',
		            previewShellSelector: '#menuItemImagePreviewShell',
		            previewImageSelector: '#menuItemImagePreview',
		            removeButtonSelector: '#menuItemImageRemove',
			        maxImageDimension: 1200,
			        outputQuality: 0.82,
		        labels: {
		            optimizing: <?= json_encode(admin_ui('optimize_image_in_progress'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
		            failed: <?= json_encode(admin_ui('optimize_image_failed'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
		        }
		    });
</script>
<?= $this->endSection() ?>
