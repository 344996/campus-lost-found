<?php
require_once __DIR__ . '/includes/init.php';

if (is_logged_in()) {
    redirect('index.php');
}

$email = '';
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    $stmt = db()->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        login_user($user);
        redirect('index.php');
    }

    $error = 'Invalid email or password.';
}
?>
<!doctype html>
<html lang="en" translate="no" class="notranslate">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="google" content="notranslate">
    <title>Login - <?= e(APP_NAME) ?></title>
    <link rel="stylesheet" href="<?= e(url('assets/css/styles.css')) ?>">
</head>
<body class="auth-body">
<main class="auth-card">
    <a class="brand large" href="<?= e(url('login.php')) ?>"><span class="brand-mark">LF</span><span><?= e(APP_NAME) ?></span></a>
    <h1>Welcome Back</h1>
    <p class="muted">Use your campus account to report, search, and recover items.</p>
    <?php if ($error): ?><div class="notice error"><?= e($error) ?></div><?php endif; ?>
    <?php foreach (consume_flash() as $message): ?>
        <div class="notice <?= e($message['type']) ?>"><?= e($message['message']) ?></div>
    <?php endforeach; ?>
    <form method="post" class="form-stack">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <label>Email
            <input type="email" name="email" value="<?= e($email) ?>" required autocomplete="email">
        </label>
        <label>Password
            <input type="password" name="password" required autocomplete="current-password">
        </label>
        <button class="button full" type="submit">Login</button>
    </form>
    <p class="muted">Demo admin: <strong>admin@ukm.edu.my</strong> / <strong>admin123</strong></p>
    <p>New user? <a href="<?= e(url('register.php')) ?>">Create an account</a></p>
</main>
</body>
</html>
