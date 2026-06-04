<?= $this->extend('layouts/superadmin') ?>

<?= $this->section('content') ?>

<header class="md:d-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="text-3xl mb-1">Edit cafe</h1>
        <p class="text-secondary mb-0"><?= esc($cafe['cafe_name'] ?: $cafe['username']) ?></p>
    </div>
    <a class="btn btn-default mt-3 md:mt-0" href="<?= site_url('superadmin') ?>">Back to cafes</a>
</header>

<div class="card mb-4">
    <div class="card-body">
        <h2 class="text-xl mb-3">Basic data</h2>
        <form method="post" action="<?= site_url('superadmin/cafes/' . $cafe['id']) ?>">
            <?= csrf_field() ?>
            <div class="row">
                <div class="md:col-4 mb-4">
                    <label class="form-label">Code</label>
                    <input type="text" name="code" class="form-control" value="<?= esc(menu_old('code', $cafe['code'] ?? '')) ?>" maxlength="6">
                </div>
                <div class="md:col-4 mb-4">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" value="<?= esc(menu_old('username', $cafe['username'])) ?>" required>
                </div>
                <div class="md:col-4 mb-4">
                    <label class="form-label">Status</label>
                    <?php $selectedStatus = menu_old('status', $cafe['status']); ?>
                    <select name="status" class="form-select" required>
                        <?php foreach (['active', 'demo', 'inactive'] as $status): ?>
                            <option value="<?= esc($status) ?>" <?= $selectedStatus === $status ? 'selected' : '' ?>><?= esc($status) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="md:col-4 mb-4">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" value="<?= esc(menu_old('phone', $cafe['phone'])) ?>" required>
                </div>
                <div class="md:col-4 mb-4">
                    <label class="form-label">Person name</label>
                    <input type="text" name="person_name" class="form-control" value="<?= esc(menu_old('person_name', $cafe['person_name'])) ?>" required>
                </div>
                <div class="md:col-4 mb-4">
                    <label class="form-label">Currency</label>
                    <input type="text" name="currency_name" class="form-control" value="<?= esc(menu_old('currency_name', $cafe['currency_name'])) ?>" required>
                </div>
            </div>
            <div class="row">
                <div class="md:col-6 mb-4">
                    <label class="form-label">Cafe name</label>
                    <input type="text" name="cafe_name" class="form-control" value="<?= esc(menu_old('cafe_name', $cafe['cafe_name'] ?? '')) ?>">
                </div>
                <div class="md:col-6 mb-4">
                    <label class="form-label">Slogan</label>
                    <input type="text" name="slogan" class="form-control" value="<?= esc(menu_old('slogan', $cafe['slogan'] ?? '')) ?>">
                </div>
            </div>

            <?php if (! empty($cafe['logo_path'])): ?>
                <div class="mb-4">
                    <label class="form-label">Logo</label>
                    <img
                        src="<?= esc(menu_asset_url($cafe['logo_path'])) ?>"
                        alt="<?= esc($cafe['cafe_name'] ?: $cafe['username']) ?>"
                        style="max-width:160px; max-height:100px; object-fit:contain; display:block;"
                    >
                    <small><?= esc($cafe['logo_path']) ?></small>
                </div>
            <?php endif; ?>

            <button type="submit" class="btn btn-primary">Save basic data</button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h2 class="text-xl mb-3">Cafe password</h2>
        <form method="post" action="<?= site_url('superadmin/cafes/' . $cafe['id'] . '/password') ?>">
            <?= csrf_field() ?>
            <div class="row">
                <div class="md:col-6 mb-4">
                    <label class="form-label">New password</label>
                    <input type="password" name="new_password" class="form-control" required>
                </div>
                <div class="md:col-6 mb-4">
                    <label class="form-label">Confirm new password</label>
                    <input type="password" name="password_confirm" class="form-control" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Save password</button>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
