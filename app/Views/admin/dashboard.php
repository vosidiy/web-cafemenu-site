<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-2 pb-3 border-bottom border-color-neutral-300">
    <div>
        <h2 class="text-3xl mb-1">Главная: <?= esc($cafe['cafe_name'] ?: $cafe['username']) ?></h2>
        <p class="mb-0">
         Username: <strong><?= esc($cafe['username']) ?></strong> •   JSON url <a href="<?= esc($publicJsonUrl) ?>" target="_blank"><?= esc($publicJsonUrl) ?></a> 
        </p>
    </div>
</div>

<div class="d-flex gap-4 flex-wrap mb-4">
    <div>
        <p>Блюда: <span class="fw-semibold"><?= esc($itemCount) ?></span></p>
    </div>
    <div>
        <p>Категории: <span class="fw-semibold"><?= esc($categoryCount) ?></span></p>
    </div>
    <div>
        <p>Версия меню: <span class="fw-semibold"><?= esc($cafe['menu_version'] ?? 1) ?></span></p>
        
    </div>
    <div>
        <p>Обновлено: <span class="fw-semibold"><?= esc($cafe['menu_updated_at'] ?? $cafe['updated_at'] ?? '-') ?></span></p>
    </div>
</div>

<?php
$groupedItems = [];

foreach ($categories as $category) {
    $groupedItems['category-' . $category['id']] = [
        'id'    => 'category-' . $category['id'],
        'label' => $category['name'],
        'items' => [],
    ];
}

$groupedItems['uncategorized'] = [
    'id'    => 'uncategorized',
    'label' => 'Без категории',
    'items' => [],
];

foreach ($items as $item) {
    $groupKey = ! empty($item['category_id']) ? 'category-' . $item['category_id'] : 'uncategorized';
    if (! array_key_exists($groupKey, $groupedItems)) {
        $groupedItems[$groupKey] = [
            'id'    => $groupKey,
            'label' => $item['category_name'] ?? 'Без категории',
            'items' => [],
        ];
    }

    $groupedItems[$groupKey]['items'][] = $item;
}

$groupedItems = array_values(array_filter($groupedItems, static fn (array $group): bool => $group['items'] !== []));
?>


<main class="card">
    <header class="p-3 bg-neutral-200 border-bottom">
        <div class="row justify-content-between">
            <div class="lg:col-5">
                <select class="form-select" data-category-filter aria-label="Фильтр блюд по категории">
                    <option value="all">Все категории</option>
                    <option value="uncategorized">Без категории</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= esc('category-' . $category['id']) ?>"><?= esc($category['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="lg:col-auto">
                <a class="btn btn-primary" href="<?= site_url('admin/menu-items/new') ?>"> + Добавить блюдо </a>
            </div>
        </div>
    </header>

    <div class="card-body">
        <?php if ($items === []): ?>
            <div class="text-center py-5 text-muted">Пункты меню пока не созданы.</div>
        <?php else: ?>
            
            <div data-menu-groups>
                <?php foreach ($groupedItems as $group): ?>
                    <section class="menu-group" data-category-group="<?= esc($group['id']) ?>">
                        <h5 class="border-bottom pb-3 mb-3"><?= esc($group['label']) ?></h5>
                        <div class="row g-3">
                            <?php foreach ($group['items'] as $item): ?>
                                <div class="col-12 md:col-6 lg:col-4">
                                    <div class="card shadow overflow-hidden mb-5">
                                        <?php if (! empty($item['image_path'])): ?>
                                            <img
                                                style="object-fit:cover; height:180px"
                                                src="<?= esc(menu_asset_url($item['image_path'])) ?>"
                                                alt="<?= esc($item['name']) ?>"
                                                class="rounded-top w-full"
                                            >
                                        <?php else: ?>
                                            <div style="object-fit:cover; background:#ddd; height:180px; text-align:center; padding:10px">
                                                Нет изображения
                                            </div>
                                        <?php endif; ?>

                                        <div class="card-body">
                                            <span class="float-right badge <?= (int) $item['is_available'] === 1 ? 'bg-green-200' : 'bg-red-200' ?>">
                                                <?= (int) $item['is_available'] === 1 ? 'Доступно' : 'Скрыто' ?>
                                            </span>
                                            
                                            <h4 class="text-lg mb-0"><?= esc($item['name']) ?></h4>

                                            <p class="text-muted">
                                                <?= esc($item['description'] ?: ' --- ') ?>
                                            </p>

                                            <div class="">📁 <?= esc($item['category_name'] ?? 'Без категории') ?></div>

                                            <div class="fw-semibold fs-5 mb-3 mt-3"><?= esc($item['price']) ?>  </div>

                                            <div class="d-grid grid-template-cols-2 gap-2">
                                                <a class="btn btn-sm flex-1 btn-outline" href="<?= site_url('admin/menu-items/' . $item['id'] . '/edit') ?>">Изменить</a>
                                                <form method="post" action="<?= site_url('admin/menu-items/' . $item['id'] . '/delete') ?>" class="flex-1 min-w-initial">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="btn btn-sm w-full btn-red-subtle" onclick="return confirm('Удалить этот пункт меню?')">Удалить</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<div class="card bg-neutral-200 mt-4">
<div class="card-body">
    <h5 class="text-lg mb-0">Страница меню:</h5>
    <a href="<?= esc($publicMenuUrl) ?>" target="_blank"><?= esc($publicMenuUrl) ?></a>
</div>
</div>

<p class="text-secondary p-2 my-4 text-center">
    Created by CafeMenu. Support: <a target="_blank" href="<?= base_url() ?>"> <?= base_url() ?> </a>
</p>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const filter = document.querySelector('[data-category-filter]');
        const groups = [...document.querySelectorAll('[data-category-group]')];

        if (!filter || groups.length === 0) {
            return;
        }

        const applyFilter = () => {
            const selectedValue = filter.value;

            groups.forEach((group) => {
                const shouldShow = selectedValue === 'all' || group.dataset.categoryGroup === selectedValue;
                group.classList.toggle('d-none', !shouldShow);
            });
        };

        filter.addEventListener('change', applyFilter);
        applyFilter();
    });
</script>



<style>
    p{ margin:0}
    .menu-group + .menu-group {
        margin-top: 2rem;
    }    
</style>

<?= $this->endSection() ?>
