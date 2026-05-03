<?php
helper('url');

$redirectUrl = base_url('/');
$adminUrl = site_url('admin');
$pageTitle = $pageTitle ?? 'Cafe Menu';
$statusCode = $statusCode ?? '';
$heading = $heading ?? 'Something went wrong';
$message = $message ?? 'Please wait a moment. You will be redirected shortly.';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <meta http-equiv="refresh" content="3;url=<?= esc($redirectUrl, 'attr') ?>">
    <title><?= esc($pageTitle) ?></title>
    <link href="<?= esc(base_url('final.min.css'), 'attr') ?>" rel="stylesheet">
</head>
<body class="bg-neutral-100 min-h-100vh">
<header class="border-bottom py-2 bg-white">
    <div class="container">
        <a href="<?= esc($redirectUrl) ?>" class="hover:opacity-80 d-flex text-decoration-none align-items-center m-0" style="width: fit-content;">
            <svg xmlns="http://www.w3.org/2000/svg" height="36px" viewBox="0 -960 960 960" width="36" fill="#b85000" aria-hidden="true"><path d="M226.67-500v-234h234v234h-234Zm0 274v-234h234v234h-234ZM500-500v-234h234v234H500Zm0 274v-234h234v234H500ZM186.67-120q-27 0-46.84-19.83Q120-159.67 120-186.67v-586.66q0-27 19.83-46.84Q159.67-840 186.67-840h586.66q27 0 46.84 19.83Q840-800.33 840-773.33v586.66q0 27-19.83 46.84Q800.33-120 773.33-120H186.67Zm0-66.67h586.66v-586.66H186.67v586.66Z"/></svg>
            <div style="font-size:24px;" class="text-orange-800 font-semibold ml-1">Cafe<span class="text-secondary">Menu</span></div>
        </a>
    </div>
</header>

<main class="py-10 lg:py-16">
    <div class="container">
        <section class="mx-auto bg-white p-5 lg:p-8 rounded-lg border" style="max-width: 720px;">
            <?php if ($statusCode !== ''): ?>
                <p class="text-orange font-semibold text-uppercase mb-3"><?= esc($statusCode) ?></p>
            <?php endif; ?>

            <h1 class="text-3xl lg:text-5xl mb-4"><?= esc($heading) ?></h1>
            <p class="text-lg text-secondary mb-6"><?= esc($message) ?></p>

            <div class="d-flex flex-col sm:flex-row gap-2">
                <a href="<?= esc($redirectUrl) ?>" class="btn btn-default">Back to Main website</a>
                <a href="<?= esc($adminUrl) ?>" class="btn btn-default">Admin page</a>
            </div>
        </section>
    </div>
</main>
</body>
</html>
