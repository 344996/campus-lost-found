<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';

$message = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = null;
        $connectedPort = null;
        $triedPorts = [];
        foreach (database_ports() as $port) {
            $triedPorts[] = $port;
            try {
                $dsn = sprintf('mysql:host=%s;port=%s;charset=utf8mb4', DB_HOST, $port);
                $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
                $connectedPort = $port;
                break;
            } catch (PDOException) {
                $pdo = null;
            }
        }
        if (!$pdo) {
            throw new RuntimeException(database_connection_error($triedPorts, false));
        }
        $sql = file_get_contents(__DIR__ . '/database/schema.sql');
        $pdo->exec($sql);
        $message = 'Database installed successfully on MySQL port ' . $connectedPort . '. Demo admin: admin@ukm.edu.my / admin123';
    } catch (Throwable $exception) {
        $error = str_starts_with($exception->getMessage(), 'Unable ')
            ? $exception->getMessage()
            : 'Installation failed. Please check that MySQL is running in XAMPP and try again.';
    }
}
?>
<!doctype html>
<html lang="en" translate="no" class="notranslate">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="google" content="notranslate">
    <title>Install - Campus Lost and Found</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body class="auth-body">
<main class="auth-card">
    <div class="brand large"><span class="brand-mark">LF</span><span>Campus Lost and Found</span></div>
    <h1>Install Database</h1>
    <p class="muted">Start Apache and MySQL in XAMPP, then run this installer once.</p>
    <?php if ($message): ?><div class="notice success"><?= htmlspecialchars($message) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="notice error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="post">
        <button class="button full" type="submit">Create Database and Tables</button>
    </form>
    <p class="muted">After installation, open <a href="login.php">login.php</a>.</p>
</main>
</body>
</html>
