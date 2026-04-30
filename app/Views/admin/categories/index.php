<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<header class="md:d-flex align-items-center justify-content-between py-4">
    <h2 class="text-3xl"> Категории </h2>
    <div class="mt-3 md:mt-0">
       <a class="btn btn-primary" href="<?= site_url('admin/categories/new') ?>">Новая категория</a> 
    </div>
</header>


<div class="card">
    <div class="card-body p-1">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                <tr>
                    <th>Иконка</th>
                    <th>Название</th>
                    <th>Порядок</th>
                    <th>Статус</th>
                    <th class="text-end">Действия</th>
                </tr>
                </thead>
                <tbody>
                <?php if ($categories === []): ?>
                    <tr><td colspan="5" class="text-center py-4 text-secondary text-lg">Категории пока не созданы.</td></tr>
                <?php endif; ?>
                <?php foreach ($categories as $category): ?>
                    <tr>
                        <td>
                            <?php if (! empty($category['icon_path'])): ?>
                                <img src="<?= esc(menu_asset_url($category['icon_path'])) ?>" alt="<?= esc($category['name']) ?>" style="max-height: 44px; max-width: 44px;">
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
                            <a class="btn btn-sm btn-default" href="<?= site_url('admin/categories/' . $category['id'] . '/edit') ?>"> 
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-pencil-icon lucide-pencil"><path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"/><path d="m15 5 4 4"/></svg>
                                Изменить
                            </a>
                            <form method="post" action="<?= site_url('admin/categories/' . $category['id'] . '/delete') ?>" class="d-inline">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-red-subtle" onclick="return confirm('Удалить эту категорию?')">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash-icon lucide-trash"><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>        
                                    Удалить
                                </button>
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
