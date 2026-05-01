<?php
$adminLanguages = $adminLanguages ?? admin_ui_supported_languages();
$adminLanguage = $adminLanguage ?? admin_ui_current_language();
$adminLanguageSwitchAction = $adminLanguageSwitchAction ?? site_url('admin/language');
$adminLanguageRedirectTo = $adminLanguageRedirectTo ?? '';
?>
<form method="post" action="<?= esc($adminLanguageSwitchAction) ?>" class="d-inline-flex align-items-center gap-2">
    <?= csrf_field() ?>
    <input type="hidden" name="redirect_to" value="<?= esc($adminLanguageRedirectTo) ?>">
    <select
        name="language"
        class="form-select"
        aria-label="<?= esc(admin_ui('menu_language_label')) ?>"
        onchange="this.form.submit()"
    >
        <?php foreach ($adminLanguages as $language): ?>
            <option value="<?= esc($language['code']) ?>" <?= ($adminLanguage['code'] ?? 'en') === $language['code'] ? 'selected' : '' ?>>
                <?= esc(trim(($language['flag'] ?? '') . ' ' . ($language['native_label'] ?? $language['code']))) ?>
            </option>
        <?php endforeach; ?>
    </select>
</form>
