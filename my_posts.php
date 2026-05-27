<?php
require_once __DIR__ . '/includes/init.php';
require_login();

$pageTitle = 'My Posts';
$stmt = db()->prepare(item_query_base() . ' WHERE items.user_id = ? ORDER BY items.created_at DESC');
$stmt->execute([current_user()['id']]);
$items = $stmt->fetchAll();

include __DIR__ . '/includes/header.php';
?>
<main class="page">
    <section class="page-head">
        <div>
            <p class="eyebrow">Personal management</p>
            <h1>My Posts</h1>
            <p class="muted">Edit or delete your reports. Edited posts return to Pending for verification.</p>
        </div>
        <a class="button" href="<?= e(url('post_item.php')) ?>">New Post</a>
    </section>
    <?php if (!$items): ?>
        <div class="empty-state">You have not created any item posts yet.</div>
    <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>Item</th>
                    <th>Type</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Location</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><a href="<?= e(url('item.php?id=' . $item['id'])) ?>"><?= e($item['item_name']) ?></a></td>
                        <td><?= e(ucfirst($item['item_type'])) ?></td>
                        <td><?= e($item['category_name']) ?></td>
                        <td><span class="badge <?= e(badge_class($item['status_name'])) ?>"><?= e($item['status_name']) ?></span></td>
                        <td><?= e($item['location']) ?></td>
                        <td><?= e($item['date_reported']) ?></td>
                        <td class="actions">
                            <a href="<?= e(url('edit_item.php?id=' . $item['id'])) ?>">Edit</a>
                            <form method="post" action="<?= e(url('delete_item.php')) ?>" data-confirm="Delete this post?">
                                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                                <button type="submit" class="link-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>

