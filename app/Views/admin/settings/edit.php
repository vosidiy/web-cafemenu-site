<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="mb-4">
    <h2 class="text-3xl mb-1">Настройки кафе</h2>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <p class="mb-3"> <b>Username / Login:</b> <?= esc($cafe['username']) ?></p>
        <form method="post" action="<?= site_url('admin/settings') ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Имя владельца</label>
                    <input type="text" name="person_name" class="form-control" value="<?= esc(menu_old('person_name', $cafe['person_name'])) ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Телефон</label>
                    <input type="text" name="phone" class="form-control" value="<?= esc(menu_old('phone', $cafe['phone'])) ?>" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Название кафе</label>
                    <input type="text" name="cafe_name" class="form-control" value="<?= esc(menu_old('cafe_name', $cafe['cafe_name'])) ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Слоган</label>
                    <input type="text" name="slogan" class="form-control" value="<?= esc(menu_old('slogan', $cafe['slogan'] ?? '')) ?>">
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">Валюта</label>
                    <input type="text" name="currency_name" class="form-control" value="<?= esc(menu_old('currency_name', $cafe['currency_name'])) ?>">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Тема оформления</label>
                    <select name="theme_style" class="form-select">
                        <?php foreach (['theme1', 'theme2', 'theme3'] as $theme): ?>
                            <option value="<?= esc($theme) ?>" <?= menu_old('theme_style', $cafe['theme_style']) === $theme ? 'selected' : '' ?>><?= esc($theme) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Адрес</label>
                <input type="text" name="address_text" class="form-control" value="<?= esc(menu_old('address_text', $cafe['address_text'])) ?>">
            </div>
            <div class="mb-4">
                <label class="form-label">Ссылка на локацию</label>
                <input type="url" name="location_url" class="form-control" value="<?= esc(menu_old('location_url', $cafe['location_url'])) ?>">
            </div>
            <?php
                $selectedLanguages = [];
                foreach ($cafeLanguages as $languageRow) {
                    $selectedLanguages[(int) ($languageRow['sort_order'] ?? 0)] = $languageRow['language_code'] ?? $languageRow['code'];
                }
            ?>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Язык по умолчанию</label>
                    <select name="languages[]" class="form-select" required>
                        <?php foreach ($supportedLanguages as $language): ?>
                            <option value="<?= esc($language['code']) ?>" <?= menu_old('languages.0', $selectedLanguages[1] ?? 'ru') === $language['code'] ? 'selected' : '' ?>>
                                <?= esc($language['flag']) ?> <?= esc($language['native_label']) ?> (<?= esc($language['label']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Доп. язык 1</label>
                    <select name="languages[]" class="form-select">
                        <option value="">Не выбран</option>
                        <?php foreach ($supportedLanguages as $language): ?>
                            <option value="<?= esc($language['code']) ?>" <?= menu_old('languages.1', $selectedLanguages[2] ?? '') === $language['code'] ? 'selected' : '' ?>>
                                <?= esc($language['flag']) ?> <?= esc($language['native_label']) ?> (<?= esc($language['label']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Доп. язык 2</label>
                    <select name="languages[]" class="form-select">
                        <option value="">Не выбран</option>
                        <?php foreach ($supportedLanguages as $language): ?>
                            <option value="<?= esc($language['code']) ?>" <?= menu_old('languages.2', $selectedLanguages[3] ?? '') === $language['code'] ? 'selected' : '' ?>>
                                <?= esc($language['flag']) ?> <?= esc($language['native_label']) ?> (<?= esc($language['label']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-4">
                    <label class="form-label">Логотип</label>
                    <input type="file" name="logo_file" class="form-control" accept=".jpg,.jpeg,.png,.webp,.svg">
                    <?php if (! empty($cafe['logo_path'])): ?>
                        <div class="mt-2"><img src="<?= esc(menu_asset_url($cafe['logo_path'])) ?>" alt="Логотип" class="img-thumbnail" style="max-height: 90px;"></div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6 mb-4">
                    <label class="form-label">PWA-иконка</label>
                    <input type="file" name="pwa_icon_file" class="form-control" accept=".jpg,.jpeg,.png,.webp,.svg">
                    <?php if (! empty($cafe['pwa_icon_path'])): ?>
                        <div class="mt-2"><img src="<?= esc(menu_asset_url($cafe['pwa_icon_path'])) ?>" alt="PWA-иконка" class="img-thumbnail" style="max-height: 90px;"></div>
                    <?php endif; ?>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Сохранить настройки</button>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm mt-4">
    <div class="card-body">
        <h2 class="h5 mb-3">Обновить пароль</h2>
        <form method="post" action="<?= site_url('admin/settings/password') ?>">
            <?= csrf_field() ?>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Старый пароль</label>
                    <input type="password" name="old_password" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Новый пароль</label>
                    <input type="password" name="new_password" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Повторите новый пароль</label>
                    <input type="password" name="new_password_confirm" class="form-control" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Обновить пароль</button>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
