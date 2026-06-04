<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Superadmin sign in') ?></title>
    <link href="<?= base_url('final.min.css') ?>" rel="stylesheet">
</head>
<body class="min-h-100vh bg-secondary">
<header class="border-bottom py-2 bg-white">
    <div class="container d-flex flex-row justify-content-between align-items-center">
        <a href="<?= base_url() ?>" class="hover:opacity-80 d-flex text-decoration-none align-items-center m-0">
            <div style="font-size:20px;" class="text-orange-800 font-semibold ml-1">CafeMenu Superadmin</div>
        </a>
    </div>
</header>

<main class="container" style="max-width:420px">
    <article class="card mt-10 md:mt-20 shadow">
        <div class="card-body md:p-8">
            <h1 class="text-xl mb-5">Superadmin sign in</h1>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert p-2 alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert p-2 alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
            <?php endif; ?>

            <form method="post" action="<?= site_url('superadmin/login') ?>">
                <?= csrf_field() ?>
                <div class="mb-4">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" value="<?= esc(menu_old('username')) ?>" required>
                </div>
                <div class="mb-4">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn w-full btn-primary">Sign in</button>
            </form>
        </div>
    </article>
</main>
</body>
</html>
