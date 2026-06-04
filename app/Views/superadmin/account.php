<?= $this->extend('layouts/superadmin') ?>

<?= $this->section('content') ?>

<header class="mb-4">
    <h1 class="text-3xl mb-1">Account</h1>
    <p class="text-secondary mb-0">Change username or password by confirming the current password.</p>
</header>

<div class="card">
    <div class="card-body">
        <form method="post" action="<?= site_url('superadmin/account') ?>">
            <?= csrf_field() ?>
            <div class="mb-4">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" value="<?= esc(menu_old('username', $admin['username'])) ?>" required>
            </div>
            <div class="mb-4">
                <label class="form-label">Current password</label>
                <input type="password" name="current_password" class="form-control" required>
            </div>
            <div class="row">
                <div class="md:col-6 mb-4">
                    <label class="form-label">New password</label>
                    <input type="password" name="new_password" class="form-control">
                </div>
                <div class="md:col-6 mb-4">
                    <label class="form-label">Confirm new password</label>
                    <input type="password" name="password_confirm" class="form-control">
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Save account</button>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
