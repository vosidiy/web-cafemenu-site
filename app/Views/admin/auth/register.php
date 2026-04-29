<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Админ-панель Cafe Menu') ?></title>
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
        <div class="">
            <a href="https://t.me/vosidiy?text=Salom alaykum, CafeMenu masalasida yordam kerak" target="_blank" class="btn btn-default"> 
                Support ↗ 
            </a>
        </div>
    </div>  <!-- container .// -->
</header>

<div class="container" style="max-width:480px">
    <article class="card mt-10 md:mt-20 shadow">
        <div class="card-body">
            <h1 class="text-xl mb-4">Создание аккаунта кафе</h1>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert p-2 alert-danger"> ⚠️ <?= esc(session()->getFlashdata('error')) ?></div>
            <?php endif; ?>

            <?php $errors = session()->getFlashdata('errors') ?? []; ?>
            <?php if ($errors !== []): ?>
                <div class="mb-2"> ⚠️ Исправьте следующие ошибки:</div>
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
                    <label class="form-label mb-0">Username</label>
                    <div class="text-sm text-secondary mb-2">Можно вводить с заглавными буквами и пробелами. Мы автоматически сохраним username в нижнем регистре без пробелов.</div>
                    <input type="text" name="username" placeholder="bestcafe" class="form-control" value="<?= esc(menu_old('username')) ?>" placeholder="bestcafe" required>
                </div>
                <div class="mb-4">
                    <label class="form-label">Название кафе</label>
                    <input type="text" name="cafe_name" placeholder="Best cafe" class="form-control" value="<?= esc(menu_old('cafe_name')) ?>">
                </div>
                <div class="mb-4">
                    <label class="form-label">Телефон</label>
                    <input type="text" name="phone" value="+998" class="form-control" value="<?= esc(menu_old('phone')) ?>" required>
                </div>
                <div class="mb-4">
                    <label class="form-label">Имя владельца</label>
                    <input type="text" name="person_name" placeholder="Person" class="form-control" value="<?= esc(menu_old('person_name')) ?>" required>
                </div>
                <div class="mb-4">
                    <label class="form-label">Пароль</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-4">
                    <label class="form-label">Подтвердите пароль</label>
                    <input type="password" name="password_confirm" class="form-control" required>
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn w-full btn-primary">Зарегистрироваться</button>
                </div>
            </form>
        </div>
    </article>

    <p class="text-center my-5">
        <a href="<?= site_url('login') ?>">Уже есть аккаунт? Login </a>
    </p>

</div>


</body>
</html>
