<!DOCTYPE html>
<html lang="<?= esc($adminLanguage['code'] ?? 'en') ?>" dir="<?= esc($adminLanguage['dir'] ?? 'ltr') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc(admin_ui($title ?? 'register_page_title')) ?></title>
    <link href="<?= base_url('final.min.css') ?>" rel="stylesheet">

    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">

</head>
<body class="min-h-100vh bg-secondary">



<header id="header" class="border-bottom py-2 bg-white">
    <div class="container d-flex flex-row justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <!-- brand -->
            <a href="<?= base_url() ?>" class="hover:opacity-80 d-flex text-decoration-none align-items-center m-0">
                <div style="font-size:20px;" class="text-orange-800 font-semibold ml-1">
                     📙 CafeMenu
                </div>
            </a>
            <!-- brand .//end -->
        </div>
        <div class="d-flex align-items-center gap-2">
            <?= view('admin/partials/language_switcher') ?>
            <a href="https://t.me/vosidiy?text=<?= rawurlencode(admin_ui('support_prefill')) ?>" target="_blank" class="btn btn-default">
                <span class="d-none md:d-inline-block"><?= esc(admin_ui('support')) ?></span> 💬 ↗
            </a>
        </div>
    </div>  <!-- container .// -->
</header>

<div class="container" style="max-width:480px">
    <article class="card mt-10 md:mt-20 shadow">
        <div class="card-body">
            <h1 class="text-xl mb-4"><?= esc(admin_ui('register_heading')) ?></h1>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert p-2 alert-danger"> ⚠️ <?= esc(session()->getFlashdata('error')) ?></div>
            <?php endif; ?>

            <?php $errors = session()->getFlashdata('errors') ?? []; ?>
            <?php if ($errors !== []): ?>
                <div class="mb-2"> ⚠️ <?= esc(admin_ui('fix_errors_heading')) ?></div>
                <div class="alert  alert-danger">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li> ⚠️ <?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <form method="post" action="<?= site_url('register') ?>">
                <?= csrf_field() ?>
                <div class="mb-4">
                    <label class="form-label mb-0"><?= esc(admin_ui('username_label')) ?></label>
                    <div class="text-sm text-secondary mb-2"><?= esc(admin_ui('username_hint')) ?></div>
                    <input type="text" name="username" class="form-control" value="<?= esc(menu_old('username')) ?>" required>
                </div>
                <div class="mb-4">
                    <label class="form-label"><?= esc(admin_ui('cafe_name_label')) ?></label>
                    <input type="text" name="cafe_name" class="form-control" value="<?= esc(menu_old('cafe_name')) ?>">
                </div>
                <div class="mb-4">
                    <label class="form-label"><?= esc(admin_ui('phone_label')) ?></label>
                    <input type="text" name="phone" class="form-control" value="<?= esc(menu_old('phone', '+998')) ?>" required>
                </div>
                <div class="mb-4">
                    <label class="form-label"><?= esc(admin_ui('owner_name_label')) ?></label>
                    <input type="text" name="person_name" class="form-control" value="<?= esc(menu_old('person_name')) ?>" required>
                </div>
                <div class="mb-4">
                    <label class="form-label"><?= esc(admin_ui('password_label')) ?></label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-4">
                    <label class="form-label"><?= esc(admin_ui('password_confirm_label')) ?></label>
                    <input type="password" name="password_confirm" class="form-control" required>
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn w-full btn-primary"><?= esc(admin_ui('sign_up')) ?></button>
                </div>
            </form>
        </div>
    </article>

    <p class="text-center my-5">
        <a href="<?= site_url('login') ?>"><?= esc(admin_ui('already_have_account')) ?> <?= esc(admin_ui('login_link_label')) ?></a>
    </p>

</div>


</body>
</html>
