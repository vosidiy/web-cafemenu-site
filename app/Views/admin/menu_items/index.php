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
            <div class="d-flex gap-2 align-items-center">
                <input
                    autofocus
                    type="search"
                    class="form-control"
                    placeholder="Поиск"
                    data-menu-search-input
                >
                <button
                    type="button"
                    class="btn btn-default d-none"
                    data-menu-search-reset
                    aria-label="Очистить поиск"
                >
                    Сбросить
                </button>
            </div>
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
                <tbody data-menu-search-body>
                <?php if ($items === []): ?>
                    <tr><td colspan="7" class="text-center py-4 text-muted">Пункты меню пока не созданы.</td></tr>
                <?php endif; ?>
                <?php foreach ($items as $item): ?>
                    <tr
                        valign="top"
                        data-menu-search-row
                        data-name="<?= esc(mb_strtolower((string) ($item['search_name'] ?? $item['name'] ?? ''), 'UTF-8')) ?>"
                        data-category="<?= esc(mb_strtolower((string) ($item['category_name'] ?? 'Без категории'), 'UTF-8')) ?>"
                    >
                        <td width="2%"> #<?= esc($item['sort_order']) ?> </td>
                        <td width="10%">
                            <?php if (! empty($item['image_path'])): ?>
                                <img class="w-30 rounded" src="<?= esc(menu_asset_url($item['image_path'])) ?>" style="max-height: 80px;">
                            <?php else: ?>
                                <img class="w-30 rounded" src="/nophoto.png" title="No photo"  style="max-height: 80px;">
                            <?php endif; ?>
                        </td>
                        <td width="30%">
                            <?php foreach (($item['translations'] ?? []) as $index => $translation): ?>
                                <div<?= $index > 0 ? ' class="mt-2"' : '' ?>>
                                    <p class="font-semibold"><?= esc(trim($translation['flag'] . ' ' . $translation['name'])) ?></p>
                                    <p><?= esc($translation['description'] ?: ' --- ') ?></p>
                                </div>
                            <?php endforeach; ?>
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
                <?php if ($items !== []): ?>
                    <tr class="d-none" data-menu-search-empty>
                        <td colspan="7" class="text-center py-4 text-muted">Ничего не найдено.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.querySelector('[data-menu-search-input]');
        const resetButton = document.querySelector('[data-menu-search-reset]');
        const rows = [...document.querySelectorAll('[data-menu-search-row]')];
        const emptyStateRow = document.querySelector('[data-menu-search-empty]');

        if (!searchInput || !resetButton || rows.length === 0 || !emptyStateRow) {
            return;
        }

        const syncResetButton = (query) => {
            const hasQuery = query.length > 0;
            resetButton.classList.toggle('d-none', !hasQuery);
            resetButton.disabled = !hasQuery;
        };

        const applySearch = () => {
            const query = searchInput.value.trim().toLowerCase();
            let visibleRows = 0;

            rows.forEach((row) => {
                const haystack = `${row.dataset.name || ''} ${row.dataset.category || ''}`;
                const shouldShow = query === '' || haystack.includes(query);

                row.classList.toggle('d-none', !shouldShow);

                if (shouldShow) {
                    visibleRows += 1;
                }
            });

            emptyStateRow.classList.toggle('d-none', visibleRows !== 0);
            syncResetButton(query);
        };

        searchInput.addEventListener('input', applySearch);
        resetButton.addEventListener('click', () => {
            searchInput.value = '';
            applySearch();
            searchInput.focus();
        });

        applySearch();
    });
</script>
<?= $this->endSection() ?>
