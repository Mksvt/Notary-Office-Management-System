<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Вхід') ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/main.css">
</head>
<body>
    <?= $content ?>
</body>
</html>
