<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<header class="md:d-flex align-items-center justify-content-between py-4">
    <h2 class="text-3xl"> Поиск блюд </h2>
    <div class="mt-3 md:mt-0">
       <a class="btn btn-primary" href="<?= site_url('admin/menu-items/new') ?>">Новое блюдо</a>
    </div>
</header>

<div class="card">
    <div class="card-body p-1">
        <div class="p-2 border-bottom">
            <input autofocus type="search" class="form-control" placeholder="Поиск">
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>Сортир.</th>
                    <th>Фото</th>
                    <th>Название</th>
                    <th>📁 Категория</th>
                    <th>Цена</th>
                    <th>Статус</th>
                    <th class="text-right">Действия</th>
                </tr>
                </thead>
                <tbody>
                <?php if ($items === []): ?>
                    <tr><td colspan="6" class="text-center py-4 text-muted">Пункты меню пока не созданы.</td></tr>
                <?php endif; ?>
                <?php foreach ($items as $item): ?>
                    <tr valign="top">
                        <td> #<?= esc($item['sort_order']) ?> </td>
                        <td width="10%">
                            <?php if (! empty($item['image_path'])): ?>
                                <img class="w-30" src="<?= esc(menu_asset_url($item['image_path'])) ?>" alt="<?= esc($item['name']) ?>" class="img-thumbnail" style="max-height: 80px;">
                            <?php else: ?>
                                <span class="text-secondary">No photo</span>
                            <?php endif; ?>
                        </td>
                        <td width="30%">
                            <p class="font-semibold"><?= esc($item['name']) ?></p>
                            <p><?= esc($item['description'] ?: ' --- ') ?></p>
                        </td>
                        <td><?= esc($item['category_name'] ?? 'Без категории') ?></td>
                        <td><?= esc($item['price']) ?></td>
                        <td>
                            <span class="badge <?= (int) $item['is_available'] === 1 ? 'bg-green-200' : 'bg-red-200' ?>">
                                <?= (int) $item['is_available'] === 1 ? 'Доступно' : 'Скрыто' ?>
                            </span>
                        </td>
                        <td class="text-right">
                            <a class="btn btn-sm btn-outline" href="<?= site_url('admin/menu-items/' . $item['id'] . '/edit') ?>">Изменить</a>
                            <form method="post" action="<?= site_url('admin/menu-items/' . $item['id'] . '/delete') ?>" class="d-inline">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-red-subtle" onclick="return confirm('Удалить этот пункт меню?')">Удалить</button>
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
