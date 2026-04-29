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

<div class="container" style="max-width:420px">
    
        <article class="card mt-10 md:mt-20 shadow">
            <div class="card-body md:p-8">
                <h1 class="text-xl mb-5">Вход в админ-панель</h1>


                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert p-2 alert-danger"> ⚠️ <?= esc(session()->getFlashdata('error')) ?></div>
                <?php endif; ?>
                        
                <form method="post" action="<?= site_url('admin/login') ?>">
                    <?= csrf_field() ?>
                    <div class="mb-4">
                        <label class="form-label">Имя пользователя</label>
                        <input type="text" name="username" placeholder="username" class="form-control" value="<?= esc(menu_old('username')) ?>" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Пароль</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mt-6">
                        <button type="submit" class="btn w-full btn-primary">Войти</button>
                    </div>
                </form>
            </div>
        </article>

        <br>

        <p hidden class="text-center my-5"> <a  href="<?= site_url('admin/register') ?>">Создать аккаунт</a></p>
</div>


</body>
</html>
