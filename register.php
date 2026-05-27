<?php
require_once __DIR__ . '/includes/init.php';

if (is_logged_in()) {
    redirect('index.php');
}

$name = '';
$email = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim((string) ($_POST['name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $confirm = (string) ($_POST['confirm_password'] ?? '');

    if ($name === '') {
        $errors[] = 'Name is required.';
    }
    if (!is_campus_email($email)) {
        $errors[] = 'Please enter a valid campus email address.';
    }
    if (strlen($password) < 6) {
        $errors[] = 'Password must contain at least 6 characters.';
    }
    if ($password !== $confirm) {
        $errors[] = 'Password confirmation does not match.';
    }

    if (!$errors) {
        try {
            $stmt = db()->prepare('INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)');
            $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT), 'user']);
            flash('Registration successful. Please log in.', 'success');
            redirect('login.php');
        } catch (PDOException $exception) {
            if ($exception->getCode() === '23000') {
                $errors[] = 'This email is already registered.';
            } else {
                $errors[] = 'Registration failed. Please try again.';
            }
        }
    }
}
?>
<!doctype html>
<html lang="en" translate="no" class="notranslate">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="google" content="notranslate">
    <title>Register - <?= e(APP_NAME) ?></title>
    <link rel="stylesheet" href="<?= e(url('assets/css/styles.css')) ?>">
</head>
<body class="auth-body">
<main class="auth-card">
    <a class="brand large" href="<?= e(url('login.php')) ?>"><span class="brand-mark">LF</span><span><?= e(APP_NAME) ?></span></a>
    <h1>Create Account</h1>
    <p class="muted">Campus members can create item reports and contact posters.</p>
    <?php foreach ($errors as $error): ?><div class="notice error"><?= e($error) ?></div><?php endforeach; ?>
    <form method="post" class="form-stack">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <label>Full Name
            <input type="text" name="name" value="<?= e($name) ?>" required>
        </label>
        <label>Campus Email
            <input type="email" name="email" value="<?= e($email) ?>" required autocomplete="email">
        </label>
        <label>Password
            <input type="password" name="password" required autocomplete="new-password">
        </label>
        <label>Confirm Password
            <input type="password" name="confirm_password" required autocomplete="new-password">
        </label>
        <button class="button full" type="submit">Register</button>
    </form>
    <p>Already registered? <a href="<?= e(url('login.php')) ?>">Login</a></p>
</main>
</body>
</html>
