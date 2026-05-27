<?php
require_once __DIR__ . '/includes/init.php';
require_login();

$pageTitle = 'Messages';
$stmt = db()->prepare("SELECT messages.*, items.item_name, items.item_type, users.name AS sender_name
    FROM messages
    JOIN items ON items.id = messages.item_id
    JOIN users ON users.id = messages.sender_id
    WHERE messages.poster_id = ?
    ORDER BY messages.created_at DESC");
$stmt->execute([current_user()['id']]);
$receivedMessages = $stmt->fetchAll();

$stmt = db()->prepare("SELECT messages.*, items.item_name, items.item_type, posters.name AS poster_name
    FROM messages
    JOIN items ON items.id = messages.item_id
    JOIN users AS posters ON posters.id = messages.poster_id
    WHERE messages.sender_id = ?
    ORDER BY messages.created_at DESC");
$stmt->execute([current_user()['id']]);
$sentMessages = $stmt->fetchAll();

include __DIR__ . '/includes/header.php';
?>
<main class="page narrow">
    <section class="page-head">
        <div>
            <p class="eyebrow">Privacy-aware contact</p>
            <h1>Messages</h1>
            <p class="muted">Read messages about your posts and review the messages you have sent to other posters.</p>
        </div>
    </section>

    <section class="message-summary" aria-label="Message summary">
        <a class="panel summary-card" href="#received">
            <span><?= count($receivedMessages) ?></span>
            <small>Received</small>
        </a>
        <a class="panel summary-card" href="#sent">
            <span><?= count($sentMessages) ?></span>
            <small>Sent</small>
        </a>
    </section>

    <section id="received" class="message-section">
        <h2>Received Messages</h2>
        <?php if (!$receivedMessages): ?>
            <div class="empty-state">No received messages yet.</div>
        <?php else: ?>
            <section class="message-list">
                <?php foreach ($receivedMessages as $message): ?>
                    <article class="panel message-card">
                        <div class="item-meta">
                            <span class="pill <?= e($message['item_type']) ?>"><?= e(ucfirst($message['item_type'])) ?></span>
                            <a href="<?= e(url('item.php?id=' . $message['item_id'])) ?>"><?= e($message['item_name']) ?></a>
                        </div>
                        <p><?= nl2br(e($message['message'])) ?></p>
                        <small>From <?= e($message['sender_name']) ?> (<?= e($message['sender_email']) ?>) on <?= e($message['created_at']) ?></small>
                    </article>
                <?php endforeach; ?>
            </section>
        <?php endif; ?>
    </section>

    <section id="sent" class="message-section">
        <h2>Sent Messages</h2>
        <?php if (!$sentMessages): ?>
            <div class="empty-state">No sent messages yet.</div>
        <?php else: ?>
            <section class="message-list">
                <?php foreach ($sentMessages as $message): ?>
                    <article class="panel message-card">
                        <div class="item-meta">
                            <span class="pill <?= e($message['item_type']) ?>"><?= e(ucfirst($message['item_type'])) ?></span>
                            <a href="<?= e(url('item.php?id=' . $message['item_id'])) ?>"><?= e($message['item_name']) ?></a>
                        </div>
                        <p><?= nl2br(e($message['message'])) ?></p>
                        <small>To <?= e($message['poster_name']) ?> on <?= e($message['created_at']) ?></small>
                    </article>
                <?php endforeach; ?>
            </section>
        <?php endif; ?>
    </section>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>
