<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<header class="md:d-flex align-items-center justify-content-between py-4">
    <h2 class="text-3xl"><?= esc(admin_ui('settings_page_title')) ?></h2>
</header>

<div class="card">
    <div class="card-body">
        <p class="mb-3"><b><?= esc(admin_ui('login_label')) ?>:</b> <?= esc($cafe['username']) ?> • <b><?= esc(admin_ui('pairing_code')) ?>:</b> <?= esc($cafe['code'] ?? '-') ?> (<?= esc(admin_ui('for_tablet_or_smartphone')) ?>)
        
        </p>
        <hr>
        <h5 class="text-xl mb-3">🏚️ <?= esc(admin_ui('restaurant_info')) ?></h5>
        <form method="post" action="<?= site_url('admin/settings') ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="row">
                <div class="md:col-6 mb-4">
                    <label class="form-label"><?= esc(admin_ui('owner_or_manager_name_label')) ?></label>
                    <input type="text" name="person_name" class="form-control" value="<?= esc(menu_old('person_name', $cafe['person_name'])) ?>" required>
                </div>
                <div class="md:col-6 mb-4">
                    <label class="form-label"><?= esc(admin_ui('phone_label')) ?></label>
                    <input type="text" name="phone" class="form-control" value="<?= esc(menu_old('phone', $cafe['phone'])) ?>" required>
                </div>
            </div>
            <div class="row">
                <div class="md:col-6 mb-4">
                    <label class="form-label"><?= esc(admin_ui('cafe_name_label')) ?></label>
                    <input type="text" name="cafe_name" class="form-control" value="<?= esc(menu_old('cafe_name', $cafe['cafe_name'])) ?>">
                </div>
                <div class="md:col-6 mb-4">
                    <label class="form-label"><?= esc(admin_ui('slogan_label')) ?></label>
                    <input type="text" name="slogan" class="form-control" value="<?= esc(menu_old('slogan', $cafe['slogan'] ?? '')) ?>">
                </div>
            </div>
            <div  class="row">
                <div class="md:col-6 mb-4">
                    <label class="form-label"><?= esc(admin_ui('address_label')) ?></label>
                    <input type="text" name="address_text" class="form-control" value="<?= esc(menu_old('address_text', $cafe['address_text'])) ?>">
                </div>
                <div class="md:col-6 mb-4">
                    <label class="form-label"><?= esc(admin_ui('location_url_label')) ?></label>
                    <input type="url" name="location_url" class="form-control" value="<?= esc(menu_old('location_url', $cafe['location_url'])) ?>">
                </div>
            </div>

            <hr>
            <h5 class="text-xl mb-3">🌐 <?= esc(admin_ui('menu_languages')) ?></h5>
            <?php
                $selectedLanguages = [];
                foreach ($cafeLanguages as $languageRow) {
                    $selectedLanguages[(int) ($languageRow['sort_order'] ?? 0)] = $languageRow['language_code'] ?? $languageRow['code'];
                }
            ?>
            <div class="row" id="languages">
                <div class="md:col-4 mb-4">
                    <label class="form-label"><?= esc(admin_ui('default_language_label')) ?></label>
                    <select name="languages[]" class="form-select" required>
                        <?php foreach ($supportedLanguages as $language): ?>
                            <option value="<?= esc($language['code']) ?>" <?= menu_old('languages.0', $selectedLanguages[1] ?? menu_configured_default_language()) === $language['code'] ? 'selected' : '' ?>>
                                <?= esc($language['flag']) ?> <?= esc($language['native_label']) ?> (<?= esc($language['label']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="md:col-4 mb-4">
                    <label class="form-label"><?= esc(admin_ui('additional_language_1')) ?></label>
                    <select name="languages[]" class="form-select">
                        <option value=""><?= esc(admin_ui('none')) ?></option>
                        <?php foreach ($supportedLanguages as $language): ?>
                            <option value="<?= esc($language['code']) ?>" <?= menu_old('languages.1', $selectedLanguages[2] ?? '') === $language['code'] ? 'selected' : '' ?>>
                                <?= esc($language['flag']) ?> <?= esc($language['native_label']) ?> (<?= esc($language['label']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="md:col-4 mb-4">
                    <label class="form-label"><?= esc(admin_ui('additional_language_2')) ?></label>
                    <select name="languages[]" class="form-select">
                        <option value=""><?= esc(admin_ui('none')) ?></option>
                        <?php foreach ($supportedLanguages as $language): ?>
                            <option value="<?= esc($language['code']) ?>" <?= menu_old('languages.2', $selectedLanguages[3] ?? '') === $language['code'] ? 'selected' : '' ?>>
                                <?= esc($language['flag']) ?> <?= esc($language['native_label']) ?> (<?= esc($language['label']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <hr>
            <h5 class="text-xl mb-3">🎨 <?= esc(admin_ui('other_settings')) ?></h5>

            <div class="row">
                <div class="md:col-4 mb-4">
                    <label class="form-label"><?= esc(admin_ui('currency_label')) ?></label>
                    <input type="text" name="currency_name" class="form-control" value="<?= esc(menu_old('currency_name', $cafe['currency_name'])) ?>">
                </div>
                <div class="md:col-4 mb-4">
                    <label class="form-label"><?= esc(admin_ui('theme_style_label')) ?></label>
                    <select name="theme_style" class="form-select">
                        <?php foreach (['theme1', 'theme2', 'theme3'] as $theme): ?>
                            <option value="<?= esc($theme) ?>" <?= menu_old('theme_style', $cafe['theme_style']) === $theme ? 'selected' : '' ?>><?= esc($theme) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="md:col-4 mb-4">
                    <label class="form-label"><?= esc(admin_ui('logo_label')) ?></label>
                    <input type="file" name="logo_file" class="form-control" accept=".jpg,.jpeg,.png,.webp,.svg">
                    <?php if (! empty($cafe['logo_path'])): ?>
                        <div class="mt-2"><img src="<?= esc(menu_asset_url($cafe['logo_path'])) ?>" alt="<?= esc(admin_ui('logo_label')) ?>" class="img-thumbnail" style="max-height: 90px;"></div>
                    <?php endif; ?>
                </div>
            </div>
            
            <hr>
            <h5 class="text-xl mb-3">💵 <?= esc(admin_ui('extra_fee_section')) ?></h5>

            <div class="form-check my-4">
                <input
                    class="form-check-input"
                    type="checkbox"
                    name="extra_fee_enabled"
                    id="extraFeeEnabled"
                    value="1"
                    <?= (int) menu_old('extra_fee_enabled', $cafe['extra_fee_enabled'] ?? 0) === 1 ? 'checked' : '' ?>
                >
                <label class="form-check-label" for="extraFeeEnabled"><?= esc(admin_ui('enable_extra_fee')) ?></label>
            </div>
            <div class="row">
                <div class="md:col-4 mb-5">
                    <label class="form-label"><?= esc(admin_ui('extra_fee_type_label')) ?></label>
                    <select name="extra_fee_type" class="form-select">
                        <option value=""><?= esc(admin_ui('not_selected')) ?></option>
                        <option value="fixed" <?= menu_old('extra_fee_type', $cafe['extra_fee_type'] ?? '') === 'fixed' ? 'selected' : '' ?>><?= esc(admin_ui('fixed_amount')) ?></option>
                        <option value="percent" <?= menu_old('extra_fee_type', $cafe['extra_fee_type'] ?? '') === 'percent' ? 'selected' : '' ?>><?= esc(admin_ui('percent_of_total')) ?></option>
                    </select>
                </div>
                <div class="md:col-4 mb-5">
                    <label class="form-label"><?= esc(admin_ui('fee_value_label')) ?></label>
                    <input
                        type="number"
                        step="0.01"
                        min="0.01"
                        name="extra_fee_value"
                        class="form-control"
                        value="<?= esc((string) menu_old('extra_fee_value', $cafe['extra_fee_value'] ?? '')) ?>"
                    >
                </div>
            </div>
            <?php $defaultLanguage = menu_old('languages.0', $selectedLanguages[1] ?? menu_configured_default_language()); ?>
            
            <div class="row">
            <?php foreach ($cafeLanguages as $language): ?>
                <?php
                    $languageCode = $language['language_code'] ?? $language['code'];
                    $savedTranslation = $feeTranslations[$languageCode] ?? [];
                ?>
                <div class="md:col-4 mb-4">
                    <label class="form-label">
                        <?= esc(admin_ui('extra_fee_name_label')) ?>: <?= esc(($language['flag'] ?? '') . ' ' . $language['native_label']) ?>
                        <?php if ($languageCode === $defaultLanguage): ?>
                            <span class="text-danger">*</span>
                        <?php endif; ?>
                    </label>
                    <input
                        type="text"
                        name="fee_translations[<?= esc($languageCode) ?>][label]"
                        class="form-control"
                        value="<?= esc(menu_old_fee_translation($languageCode, 'label', $savedTranslation['label'] ?? '')) ?>"
                    >
                </div>
            <?php endforeach; ?>
            </div>

            <hr>
            
            <button type="submit" class="btn btn-primary"><?= esc(admin_ui('save_settings')) ?></button>
        </form>
    </div>
</div>

<div class="card my-5">
    <div class="card-body">
        <h5 class="text-xl mb-3">🔒 <?= esc(admin_ui('change_password')) ?></h5>
        <form method="post" action="<?= site_url('admin/settings/password') ?>">
            <?= csrf_field() ?>
            <div class="row">
                <div class="md:col-4 mb-4">
                    <label class="form-label"><?= esc(admin_ui('current_password_label')) ?></label>
                    <input type="password" name="old_password" class="form-control" required>
                </div>
                <div class="md:col-4 mb-4">
                    <label class="form-label"><?= esc(admin_ui('new_password_label')) ?></label>
                    <input type="password" name="new_password" class="form-control" required>
                </div>
                <div class="md:col-4 mb-4">
                    <label class="form-label"><?= esc(admin_ui('repeat_new_password_label')) ?></label>
                    <input type="password" name="new_password_confirm" class="form-control" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary"><?= esc(admin_ui('update_password')) ?></button>
        </form>
    </div>
</div>

<br><br>

<?= $this->endSection() ?>
