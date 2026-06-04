<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Superadmin') ?></title>
    <link href="<?= base_url('final.min.css') ?>" rel="stylesheet">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <?= $this->renderSection('head') ?>
</head>
<body class="min-h-100vh bg-secondary">
<header class="border-bottom py-2 bg-white">
    <div class="container md:d-flex md:flex-row md:justify-content-between md:align-items-center">
        <a href="<?= site_url('superadmin') ?>" class="hover:opacity-80 d-flex text-decoration-none align-items-center m-0">
            <div style="font-size:20px;" class="text-orange-800 font-semibold ml-1">CafeMenu Superadmin</div>
        </a>

        <?php if (session('superadmin_id')): ?>
        <nav class="mt-3 md:mt-0">
            <ul class="nav nav-col md:flex-row">
                <li><a class="nav-link" href="<?= site_url('superadmin') ?>">Cafes</a></li>
                <li><a class="nav-link" href="<?= site_url('superadmin/settings') ?>">Settings</a></li>
                <li><a class="nav-link" href="<?= site_url('superadmin/account') ?>">Account</a></li>
                <li><a class="nav-link" href="<?= site_url('superadmin/logout') ?>">Log out</a></li>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</header>

<main class="container py-4">
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert mt-2 alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert mt-2 alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <?php $errors = session()->getFlashdata('errors') ?? []; ?>
    <?php if ($errors !== []): ?>
        <div class="alert mt-2 alert-danger">
            <h6 class="mb-1">Please fix the following errors:</h6>
            <?php foreach ($errors as $error): ?>
                <p class="mb-1"><?= esc($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?= $this->renderSection('content') ?>
</main>

<?= $this->renderSection('scripts') ?>
</body>
</html>
