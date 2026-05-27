<?php
$pageTitle = $pageTitle ?? APP_NAME;
$user = current_user();
?>
<!doctype html>
<html lang="en" translate="no" class="notranslate">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="google" content="notranslate">
    <title><?= e($pageTitle) ?> - <?= e(APP_NAME) ?></title>
    <link rel="stylesheet" href="<?= e(url('assets/css/styles.css')) ?>">
</head>
<body>
<header class="topbar">
    <a class="brand" href="<?= e(url('index.php')) ?>">
        <span class="brand-mark">LF</span>
        <span><?= e(APP_NAME) ?></span>
    </a>
    <nav class="nav">
        <?php if ($user): ?>
            <a href="<?= e(url('index.php')) ?>">Items</a>
            <a href="<?= e(url('post_item.php')) ?>">Post Item</a>
            <a href="<?= e(url('my_posts.php')) ?>">My Posts</a>
            <a href="<?= e(url('messages.php')) ?>">Messages</a>
            <?php if (is_admin()): ?>
                <a href="<?= e(url('admin/index.php')) ?>">Admin</a>
            <?php endif; ?>
            <span class="user-chip"><?= e($user['name']) ?></span>
            <a href="<?= e(url('logout.php')) ?>">Logout</a>
        <?php else: ?>
            <a href="<?= e(url('login.php')) ?>">Login</a>
            <a class="button small" href="<?= e(url('register.php')) ?>">Register</a>
        <?php endif; ?>
    </nav>
</header>
<?php foreach (consume_flash() as $message): ?>
    <div class="flash <?= e($message['type']) ?>"><?= e($message['message']) ?></div>
<?php endforeach; ?>
