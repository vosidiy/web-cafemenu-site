<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Админ-панель Cafe Menu') ?></title>
    <link href="<?= base_url('final.min.css') ?>" rel="stylesheet">

    <link rel="apple-touch-icon" sizes="180x180" href="apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon-32x32.png">
    
</head>
<body class="min-h-100vh bg-secondary">

<header id="header" class="border-bottom py-2 bg-white">
    <div class="container md:d-flex md:flex-row md:justify-content-between md:align-items-center">
        <div class="d-flex align-items-center">
            <!-- brand -->
            <a href="<?= site_url('admin') ?>" class="hover:opacity-80 d-flex text-decoration-none align-items-center m-0">
                <div style="font-size:20px;" class="text-orange-800 font-semibold ml-1">
                    📙 Админ CafeMenu 
                </div>
            </a>
            <!-- brand .//end -->
            <!-- mobile-actions -->
            <div class="md:d-none ml-auto">
                <a href="<?= site_url(session('username')) ?>" target="_blank" class="btn btn-default"> Menu ↗ </a>
                <button title="Menu" class="btn btn-neutral ml-1 btn-icon" onclick="toggleMenu()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-menu-icon lucide-menu"><path d="M4 5h16"/><path d="M4 12h16"/><path d="M4 19h16"/></svg>
                </button>
            </div>
            <!-- mobile-actions -->
        </div>

        <?php if (session('cafe_id')): ?>
        <nav id="menu_header" class="d-none md:d-block">
            <ul class="nav nav-col md:flex-row mt-4 md:mt-0">
                <li><a class="nav-link" href="<?= site_url('admin') ?>">Главная</a></li>
                <li><a class="nav-link" href="<?= site_url('admin/settings') ?>">Настройки</a></li>
                <li><a class="nav-link" href="<?= site_url('admin/categories') ?>">Категории</a></li>
                <li><a class="nav-link" href="<?= site_url('admin/menu-items') ?>">Поиск блюд</a></li>
            </ul>
        </nav>
        <?php endif; ?>

        <div class="d-none md:d-block">
            <?php if (session('username')): ?>
            <a href="<?= site_url(session('username')) ?>" target="_blank" class="btn btn-default"> Открыть меню ↗ </a>
            <?php endif; ?>
            <a href="<?= site_url('logout') ?>" class="btn btn-default"> Выйти </a>
        </div>

    </div>  <!-- container .// -->
</header>

<main class="container">

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert p-2 alert-success">  ✅  <?= esc(session()->getFlashdata('success')) ?></div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert p-2 alert-danger"> ⚠️ <?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>

<?php $errors = session()->getFlashdata('errors') ?? []; ?>
<?php if ($errors !== []): ?>
    <div class="alert alert-danger">
        <h6 class="mb-1">Исправьте следующие ошибки:</h6>
        <?php foreach ($errors as $error): ?>
        <p class="mb-1">  ⚠️   <?= esc($error) ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?= $this->renderSection('content') ?>


</main>

<script>
    // Mobile menu  toggle
    function toggleMenu() {
        document.getElementById('menu_header').classList.toggle('d-none');
    }

    function open_dialog(dialog_id){
        document.body.classList.add('overflow-hidden', 'dialog-open');
        document.getElementById(dialog_id).showModal();
    }

    function close_dialog(btn_close){
        document.body.classList.remove('overflow-hidden', 'dialog-open');
        let this_dialog = btn_close.closest('dialog')
        this_dialog.close();    
    }
</script>

</body>
</html>
