<?php
require_once __DIR__ . '/includes/init.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('my_posts.php');
}

verify_csrf();
$id = (int) ($_POST['id'] ?? 0);

$stmt = db()->prepare('SELECT * FROM items WHERE id = ?');
$stmt->execute([$id]);
$item = $stmt->fetch();

if (!$item || !can_manage_item($item)) {
    flash('You are not allowed to delete that post.', 'error');
    redirect('my_posts.php');
}

if (!empty($item['image_path'])) {
    $path = __DIR__ . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $item['image_path']);
    if (is_file($path)) {
        unlink($path);
    }
}

$stmt = db()->prepare('DELETE FROM items WHERE id = ?');
$stmt->execute([$id]);
flash('Post deleted.', 'success');
redirect(is_admin() ? 'admin/posts.php' : 'my_posts.php');

