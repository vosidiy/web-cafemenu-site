<?= $this->extend('layouts/superadmin') ?>

<?= $this->section('content') ?>

<header class="md:d-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="text-3xl mb-1">Cafes</h1>
        <p class="text-secondary">All cafe records from the database.</p>
    </div>
    <a class="btn btn-default mt-3 md:mt-0" href="<?= site_url('admin') ?>" target="_blank">Open cafe admin</a>
</header>

<div class="card">
    <div class="card-body">
        <?php if ($cafes === []): ?>
            <p class="text-secondary mb-0">No cafes found.</p>
        <?php else: ?>
            <div style="overflow-x:auto">
                <table class="table table-bordered table-sm">
                    <thead>
                    <tr>
                        <?php foreach ($columns as $column): ?>
                            <th><?= esc($column) ?></th>
                        <?php endforeach; ?>
                        <th>actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($cafes as $cafe): ?>
                        <tr>
                            <?php foreach ($columns as $column): ?>
                                <td>
                                    <?php if ($column === 'logo_path'): ?>
                                        <?php if (! empty($cafe['logo_path'])): ?>
                                            <img
                                                src="<?= esc(menu_asset_url($cafe['logo_path'])) ?>"
                                                alt="<?= esc($cafe['cafe_name'] ?: $cafe['username']) ?>"
                                                style="max-width:90px; max-height:60px; object-fit:contain; display:block;"
                                            >
                                            <small><?= esc($cafe['logo_path']) ?></small>
                                        <?php else: ?>
                                            <span class="text-secondary">-</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <?= esc((string) ($cafe[$column] ?? '')) ?>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                            <td>
                                <a class="btn btn-sm btn-outline" href="<?= site_url('superadmin/cafes/' . $cafe['id'] . '/edit') ?>">Edit</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
