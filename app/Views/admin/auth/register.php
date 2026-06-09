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
    <article class="card mt-8 md:mt-10 shadow">
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
                    <label class="form-label font-semibold"><?= esc(admin_ui('username_label')) ?></label>
                    <input type="text" name="username" placeholder="username" class="form-control" value="<?= esc(menu_old('username')) ?>" required>
                    <div class="text-sm mt-1 text-secondary mb-2"><?= esc(admin_ui('username_hint')) ?></div>
                </div>
                <div class="mb-4">
                    <label class="form-label"><?= esc(admin_ui('cafe_name_label')) ?></label>
                    <input type="text" name="cafe_name"  placeholder="Great Cafe" class="form-control" value="<?= esc(menu_old('cafe_name')) ?>">
                </div>
                <div class="mb-4">
                    <label class="form-label"><?= esc(admin_ui('phone_label')) ?></label>
                    <input type="text" name="phone" class="form-control" value="<?= esc(menu_old('phone')) ?>" required>
                </div>
                <div class="mb-4">
                    <label class="form-label"><?= esc(admin_ui('currency_label')) ?></label>
                    <input type="text" name="currency_name" maxlength="6" placeholder="USD" class="form-control" value="<?= esc(menu_old('currency_name')) ?>">
                </div>
                <div class="mb-4">
                    <label class="form-label"><?= esc(admin_ui('owner_name_label')) ?></label>
                    <input type="text" name="person_name" placeholder="Full name" class="form-control" value="<?= esc(menu_old('person_name')) ?>" required>
                </div>
                <hr>
                <div class="mb-4">
                    <label class="form-label"><?= esc(admin_ui('password_label')) ?> </label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-4">
                    <label class="form-label"><?= esc(admin_ui('password_confirm_label')) ?></label>
                    <input type="password" name="password_confirm" class="form-control" required>
                </div>
                <div class="mt-6">
                    <button type="submit" class="btn w-full btn-primary"><?= esc(admin_ui('sign_up')) ?></button>
                </div>
            </form>
        </div>
    </article>

    <p class="text-center my-5"> <?= esc(admin_ui('already_have_account')) ?> <br>
        <a class="font-semibold" href="<?= site_url('login') ?>"> <?= esc(admin_ui('login_link_label')) ?> (Admin) </a>
    </p>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const phoneInput = document.querySelector('input[name="phone"]');
    const currencyInput = document.querySelector('input[name="currency_name"]');

    if (!phoneInput && !currencyInput) {
        return;
    }

    const controller = new AbortController();
    const timeoutId = window.setTimeout(function () {
        controller.abort();
    }, 3500);

    fetch('https://ipapi.co/json/', {
        signal: controller.signal,
        headers: {
            Accept: 'application/json',
        },
    })
        .then(function (response) {
            if (!response.ok) {
                throw new Error('IP lookup failed');
            }

            return response.json();
        })
        .then(function (data) {
            const callingCode = typeof data.country_calling_code === 'string' ? data.country_calling_code.trim() : '';
            const currency = typeof data.currency === 'string' ? data.currency.trim().slice(0, 6) : '';

            if (phoneInput && phoneInput.value.trim() === '' && callingCode !== '') {
                phoneInput.value = callingCode;
            }

            if (currencyInput && currencyInput.value.trim() === '' && currency !== '') {
                currencyInput.value = currency.toUpperCase();
            }
        })
        .catch(function () {
            // Registration must keep working when geolocation is unavailable.
        })
        .finally(function () {
            window.clearTimeout(timeoutId);
        });
});
</script>

</body>
</html>
