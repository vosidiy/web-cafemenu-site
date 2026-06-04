<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($cafe['cafe_name'] ?: $username) ?></title>
    <link rel="stylesheet" href="<?= esc(base_url('final.min.css')) ?>">
</head>
<body>
    <main>
       <div class="container">
         <h1 class="my-5">Cafe is deactivated</h1>
            <p><a href="<?= esc($activationUrl ?? '#') ?>" class="btn btn-lg btn-primary">Activate cafe</a></p>
    <hr>
            <p><a href="<?= base_url() ?>">Need Help? Visit: <?= base_url() ?> </a></p>
       </div>
    </main>
</body>
</html>
