<?php
declare(strict_types=1);

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function url(string $path = ''): string
{
    return BASE_URL . '/' . ltrim($path, '/');
}

function redirect(string $path): never
{
    header('Location: ' . url($path));
    exit;
}

function flash(string $message, string $type = 'info'): void
{
    $_SESSION['flash'][] = ['message' => $message, 'type' => $type];
}

function consume_flash(): array
{
    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $messages;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(419);
        exit('Invalid request token.');
    }
}

function is_campus_email(string $email): bool
{
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    $domains = CAMPUS_EMAIL_DOMAINS;
    if (!$domains) {
        return true;
    }

    $domain = strtolower(substr(strrchr($email, '@'), 1) ?: '');
    foreach ($domains as $allowed) {
        $allowed = strtolower((string) $allowed);
        if ($domain === $allowed || str_ends_with($domain, '.' . $allowed)) {
            return true;
        }
    }
    return false;
}

function categories(): array
{
    return db()->query('SELECT id, name FROM categories ORDER BY name')->fetchAll();
}

function location_options(?string $selected = null): array
{
    $locations = LOCATION_OPTIONS;
    $selected = trim((string) $selected);
    if ($selected !== '' && !in_array($selected, $locations, true)) {
        array_unshift($locations, $selected);
    }
    return $locations;
}

function status_id(string $name): int
{
    $stmt = db()->prepare('SELECT id FROM post_statuses WHERE name = ?');
    $stmt->execute([$name]);
    return (int) $stmt->fetchColumn();
}

function badge_class(string $status): string
{
    return match (strtolower($status)) {
        'approved' => 'success',
        'rejected' => 'danger',
        default => 'pending',
    };
}

function save_uploaded_image(string $field): ?string
{
    if (empty($_FILES[$field]['name'])) {
        return null;
    }

    $file = $_FILES[$field];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Image upload failed. Please try again.');
    }
    if ($file['size'] > MAX_UPLOAD_BYTES) {
        throw new RuntimeException('Image must be smaller than 3 MB.');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    $extensions = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
    ];
    if (!isset($extensions[$mime])) {
        throw new RuntimeException('Only JPG, PNG, WebP, or GIF images are allowed.');
    }

    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0775, true);
    }

    $filename = bin2hex(random_bytes(12)) . '.' . $extensions[$mime];
    $target = UPLOAD_DIR . DIRECTORY_SEPARATOR . $filename;
    if (!move_uploaded_file($file['tmp_name'], $target)) {
        throw new RuntimeException('Unable to save uploaded image.');
    }

    return 'uploads/' . $filename;
}

function item_image(?string $path): string
{
    if ($path && is_file(dirname(__DIR__) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $path))) {
        return url($path);
    }
    return url('assets/img-placeholder.php');
}

function has_item_image(?string $path): bool
{
    return (bool) ($path && is_file(dirname(__DIR__) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $path)));
}

function excerpt(string $text, int $length = 130): string
{
    $text = trim(preg_replace('/\s+/', ' ', $text) ?? '');
    if (function_exists('mb_strimwidth')) {
        return mb_strimwidth($text, 0, $length, '...');
    }
    return strlen($text) > $length ? substr($text, 0, $length - 3) . '...' : $text;
}

function item_query_base(): string
{
    return "SELECT items.*, users.name AS poster_name, users.email AS poster_email,
                   categories.name AS category_name, post_statuses.name AS status_name
            FROM items
            JOIN users ON users.id = items.user_id
            JOIN categories ON categories.id = items.category_id
            JOIN post_statuses ON post_statuses.id = items.status_id";
}

function build_item_filters(array $input, bool $approvedOnly = true): array
{
    $where = [];
    $params = [];

    if ($approvedOnly) {
        $where[] = "post_statuses.name = 'Approved'";
    }
    if (!empty($input['q'])) {
        $where[] = '(items.item_name LIKE ? OR items.description LIKE ? OR items.location LIKE ? OR items.color LIKE ? OR items.shape LIKE ? OR items.item_size LIKE ? OR items.estimated_weight LIKE ? OR categories.name LIKE ?)';
        $needle = '%' . trim((string) $input['q']) . '%';
        array_push($params, $needle, $needle, $needle, $needle, $needle, $needle, $needle, $needle);
    }
    if (!empty($input['type'])) {
        $where[] = 'items.item_type = ?';
        $params[] = $input['type'];
    }
    if (!empty($input['category_id'])) {
        $where[] = 'items.category_id = ?';
        $params[] = (int) $input['category_id'];
    }
    if (!empty($input['location'])) {
        $where[] = 'items.location = ?';
        $params[] = trim((string) $input['location']);
    }
    foreach (['color', 'shape', 'item_size', 'estimated_weight'] as $field) {
        if (!empty($input[$field])) {
            $where[] = 'items.' . $field . ' LIKE ?';
            $params[] = '%' . trim((string) $input[$field]) . '%';
        }
    }
    if (!empty($input['date_from'])) {
        $where[] = 'items.date_reported >= ?';
        $params[] = $input['date_from'];
    }
    if (!empty($input['date_to'])) {
        $where[] = 'items.date_reported <= ?';
        $params[] = $input['date_to'];
    }
    if (!empty($input['status_id'])) {
        $where[] = 'items.status_id = ?';
        $params[] = (int) $input['status_id'];
    }

    return [$where ? ' WHERE ' . implode(' AND ', $where) : '', $params];
}

function can_manage_item(array $item): bool
{
    $user = current_user();
    return $user && (is_admin() || (int) $item['user_id'] === (int) $user['id']);
}
