<?php
declare(strict_types=1);

date_default_timezone_set('Asia/Kuala_Lumpur');

define('DB_HOST', '127.0.0.1');
// Most XAMPP installations use 3306. The app also tries 3307 automatically
// because some computers already have another MySQL service on 3306.
define('DB_PORT', '3306');
define('DB_FALLBACK_PORTS', ['3307']);
define('DB_NAME', 'campus_lost_found');
define('DB_USER', 'root');
define('DB_PASS', '');

// Registration is limited to campus email accounts as required by the report.
define('CAMPUS_EMAIL_DOMAINS', ['siswa.ukm.edu.my', 'ukm.edu.my']);

define('LOCATION_OPTIONS', [
    'Faculty of Social Sciences and Humanities',
    'Faculty of Law',
    'Faculty of Science and Technology',
    'Faculty of Education',
    'Faculty of Engineering and Built Environment',
    'Faculty of Dentistry',
    'Faculty of Economics and Management',
    'Faculty of Information Science and Technology',
    'Faculty of Islamic Studies',
    'Faculty of Health Sciences',
    'UKM-GSB Graduate School of Business',
    'Kolej Keris Mas',
    'Center for Shaping Advanced and Professional Education',
    'Pusat Kesihatan / Health Center',
    'Pusanika',
    'Perpustakaan / Tun Seri Lanang Library',
]);

define('APP_NAME', 'Campus Lost and Found');
define('UPLOAD_DIR', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads');
define('MAX_UPLOAD_BYTES', 3 * 1024 * 1024);

function database_ports(): array
{
    $ports = [DB_PORT];
    foreach (DB_FALLBACK_PORTS as $port) {
        $ports[] = (string) $port;
    }

    return array_values(array_unique(array_filter($ports, static fn (string $port): bool => $port !== '')));
}

function database_connection_error(array $ports, bool $databaseRequired): string
{
    $tried = implode(', ', $ports);
    if ($databaseRequired) {
        return 'Unable to open the application database. Start MySQL in XAMPP, run install.php first, and check includes/config.php if MySQL uses a custom port. Tried ports: ' . $tried . '.';
    }

    return 'Unable to connect to MySQL. Start MySQL in XAMPP and click Create Database and Tables again. If MySQL uses a custom port, edit includes/config.php. Tried ports: ' . $tried . '.';
}

$basePath = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
if (str_ends_with($basePath, '/admin')) {
    $basePath = dirname($basePath);
}
if ($basePath === '/' || $basePath === '\\' || $basePath === '.') {
    $basePath = '';
}
define('BASE_URL', rtrim($basePath, '/'));
