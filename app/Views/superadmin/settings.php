<?= $this->extend('layouts/superadmin') ?>

<?= $this->section('content') ?>

<header class="mb-4">
    <h1 class="text-3xl mb-1">Settings</h1>
    <p class="text-secondary mb-0">Global links and activation URL.</p>
</header>

<div class="card">
    <div class="card-body">
        <form method="post" action="<?= site_url('superadmin/settings') ?>">
            <?= csrf_field() ?>

            <?php
                $labels = [
                    'contact_url' => 'Contact URL or messenger link',
                    'social_page_link' => 'Social page link',
                    'app_link_store_normal' => 'Google Play app link',
                    'app_link_store_kiosk' => 'Google Play kiosk app link',
                    'app_link_local_normal' => 'Local normal app link',
                    'app_link_local_kiosk' => 'Local kiosk app link',
                    'activation_url' => 'Activation URL',
                ];
            ?>

            <?php foreach ($fields as $field): ?>
                <div class="mb-4">
                    <label class="form-label"><?= esc($labels[$field] ?? $field) ?></label>
                    <input
                        type="text"
                        name="<?= esc($field) ?>"
                        class="form-control"
                        value="<?= esc(menu_old($field, $adminSettings[$field] ?? '')) ?>"
                    >
                </div>
            <?php endforeach; ?>

            <button type="submit" class="btn btn-primary">Save settings</button>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
