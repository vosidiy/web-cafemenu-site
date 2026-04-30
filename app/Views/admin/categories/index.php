<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-3xl mb-1">Категории</h2>
    <a class="btn btn-primary" href="<?= site_url('admin/categories/new') ?>">Новая категория</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                <tr>
                    <th>Иконка</th>
                    <th>Название</th>
                    <th>Порядок сортировки</th>
                    <th>Статус</th>
                    <th class="text-end">Действия</th>
                </tr>
                </thead>
                <tbody>
                <?php if ($categories === []): ?>
                    <tr><td colspan="5" class="text-center py-4 text-muted">Категории пока не созданы.</td></tr>
                <?php endif; ?>
                <?php foreach ($categories as $category): ?>
                    <tr>
                        <td>
                            <?php if (! empty($category['icon_path'])): ?>
                                <img src="<?= esc(menu_asset_url($category['icon_path'])) ?>" alt="<?= esc($category['name']) ?>" class="img-thumbnail" style="max-height: 44px; max-width: 44px;">
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td><?= esc($category['name']) ?></td>
                        <td><?= esc($category['sort_order']) ?></td>
                        <td>
                            <span class="badge <?= (int) $category['is_active'] === 1 ? 'text-bg-success' : 'text-bg-secondary' ?>">
                                <?= (int) $category['is_active'] === 1 ? 'Активна' : 'Отключена' ?>
                            </span>
                        </td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-primary" href="<?= site_url('admin/categories/' . $category['id'] . '/edit') ?>">Изменить</a>
                            <form method="post" action="<?= site_url('admin/categories/' . $category['id'] . '/delete') ?>" class="d-inline">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Удалить эту категорию?')">Удалить</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
