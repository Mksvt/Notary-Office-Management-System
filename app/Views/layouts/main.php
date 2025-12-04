<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Система нотаріальної контори') ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/main.css">
</head>
<body>
<header class="site-header">
    <div class="container">
        <a href="<?= BASE_URL ?>/" class="logo">Нотаріальна контора</a>
        <nav class="main-nav">
            <a href="<?= BASE_URL ?>/cases">Справи</a>
            <a href="<?= BASE_URL ?>/clients">Клієнти</a>
            <?php if ($_SESSION['user_role'] === 'admin'): ?>
                <a href="<?= BASE_URL ?>/notaries">Нотаріуси</a>
                <a href="<?= BASE_URL ?>/offices">Офіси</a>
                <a href="<?= BASE_URL ?>/services">Послуги</a>
            <?php endif; ?>
            <a href="<?= BASE_URL ?>/payments">Платежі</a>
        </nav>
        <div class="user-info">
            <?= htmlspecialchars($_SESSION['username'] ?? 'Користувач') ?> |
            <a href="<?= BASE_URL ?>/logout" style="color: #ffffff;">Вихід</a>
        </div>
    </div>
</header>

<main class="content container">
    <?php if (isset($flash) && $flash): ?>
        <div class="alert alert-<?= htmlspecialchars($flash['type']) ?>">
            <?= htmlspecialchars($flash['message']) ?>
        </div>
    <?php endif; ?>

    <?= $content ?>
</main>

<footer class="site-footer">
    <div class="container">
        <p>&copy; <?= date('Y') ?> Система управління нотаріальною конторою</p>
    </div>
</footer>
</body>
</html>
