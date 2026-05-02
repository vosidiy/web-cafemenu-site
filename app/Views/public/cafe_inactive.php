<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($cafe['cafe_name'] ?: $username) ?></title>
</head>
<body>
    <main>
        <h1>Cafe is deactivated</h1>
        <p><a href="<?= esc($activationUrl ?? '#') ?>">Activate cafe</a></p>
    </main>
</body>
</html>
