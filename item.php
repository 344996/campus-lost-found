<?php
require_once __DIR__ . '/includes/init.php';
require_login();

$id = (int) ($_GET['id'] ?? 0);
$stmt = db()->prepare(item_query_base() . ' WHERE items.id = ?');
$stmt->execute([$id]);
$item = $stmt->fetch();

if (!$item || ($item['status_name'] !== 'Approved' && !can_manage_item($item))) {
    http_response_code(404);
    $pageTitle = 'Item Not Found';
    include __DIR__ . '/includes/header.php';
    echo '<main class="page narrow"><div class="empty-state">Item not found or not available.</div></main>';
    include __DIR__ . '/includes/footer.php';
    exit;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $message = trim((string) ($_POST['message'] ?? ''));
    if ($message === '') {
        $errors[] = 'Please enter a message.';
    } elseif ((int) $item['user_id'] === (int) current_user()['id']) {
        $errors[] = 'You cannot contact yourself for your own post.';
    } else {
        $stmt = db()->prepare('INSERT INTO messages (item_id, sender_id, poster_id, sender_email, message) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$item['id'], current_user()['id'], $item['user_id'], current_user()['email'], $message]);
        flash('Message sent to the poster.', 'success');
        redirect('messages.php#sent');
    }
}

$pageTitle = $item['item_name'];
include __DIR__ . '/includes/header.php';
?>
<main class="page">
    <section class="detail-layout">
        <div class="detail-media">
            <?php if (has_item_image($item['image_path'])): ?>
                <img src="<?= e(item_image($item['image_path'])) ?>" alt="<?= e($item['item_name']) ?>">
            <?php else: ?>
                <div class="no-photo-placeholder">No photo uploaded</div>
            <?php endif; ?>
        </div>
        <article class="panel detail-panel">
            <div class="item-meta">
                <span class="pill <?= e($item['item_type']) ?>"><?= e(ucfirst($item['item_type'])) ?></span>
                <span class="badge <?= e(badge_class($item['status_name'])) ?>"><?= e($item['status_name']) ?></span>
                <span><?= e($item['category_name']) ?></span>
            </div>
            <h1><?= e($item['item_name']) ?></h1>
            <p><?= nl2br(e($item['description'])) ?></p>
            <dl class="detail-list">
                <div><dt>Location</dt><dd><?= e($item['location']) ?></dd></div>
                <div><dt>Date</dt><dd><?= e($item['date_reported']) ?></dd></div>
                <div><dt>Color</dt><dd><?= e($item['color'] ?: '-') ?></dd></div>
                <div><dt>Shape</dt><dd><?= e($item['shape'] ?: '-') ?></dd></div>
                <div><dt>Size</dt><dd><?= e($item['item_size'] ?: '-') ?></dd></div>
                <div><dt>Weight</dt><dd><?= e($item['estimated_weight'] ?: '-') ?></dd></div>
                <div><dt>Poster</dt><dd><?= e($item['poster_name']) ?></dd></div>
            </dl>
            <?php if (can_manage_item($item)): ?>
                <div class="form-actions">
                    <a class="button" href="<?= e(url('edit_item.php?id=' . $item['id'])) ?>">Edit</a>
                    <a class="button ghost" href="<?= e(url('my_posts.php')) ?>">My Posts</a>
                </div>
            <?php endif; ?>
        </article>
    </section>

    <section class="panel contact-panel" id="contact">
        <h2>Message Poster</h2>
        <p class="muted">Your email is stored with the message, but the poster's email is not displayed publicly.</p>
        <?php foreach ($errors as $error): ?><div class="notice error"><?= e($error) ?></div><?php endforeach; ?>
        <?php if ((int) $item['user_id'] === (int) current_user()['id']): ?>
            <div class="notice info">This is your own post.</div>
        <?php else: ?>
            <form method="post" class="form-stack">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <label>Message
                    <textarea name="message" rows="4" required placeholder="Describe why you think this item may be yours, or how you can help return it."></textarea>
                </label>
                <button class="button" type="submit">Send Message</button>
            </form>
        <?php endif; ?>
    </section>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>
