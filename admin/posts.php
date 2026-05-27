<?php
require_once dirname(__DIR__) . '/includes/init.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $id = (int) ($_POST['id'] ?? 0);
    $action = (string) ($_POST['action'] ?? '');

    $stmt = db()->prepare('SELECT id FROM items WHERE id = ?');
    $stmt->execute([$id]);
    if (!$stmt->fetch()) {
        flash('Post not found.', 'error');
        redirect('admin/posts.php');
    }

    if (in_array($action, ['Approved', 'Rejected'], true)) {
        $stmt = db()->prepare('UPDATE items SET status_id = ? WHERE id = ?');
        $stmt->execute([status_id($action), $id]);
        flash('Post marked as ' . $action . '.', 'success');
    } elseif ($action === 'delete') {
        $stmt = db()->prepare('DELETE FROM items WHERE id = ?');
        $stmt->execute([$id]);
        flash('Post deleted.', 'success');
    }
    redirect('admin/posts.php');
}

$pageTitle = 'Manage Posts';
$statuses = db()->query('SELECT id, name FROM post_statuses ORDER BY id')->fetchAll();
$cats = categories();
$locations = location_options($_GET['location'] ?? '');
[$where, $params] = build_item_filters($_GET, false);
$sql = item_query_base() . $where . ' ORDER BY items.created_at DESC';
$stmt = db()->prepare($sql);
$stmt->execute($params);
$items = $stmt->fetchAll();

include dirname(__DIR__) . '/includes/header.php';
?>
<main class="page">
    <section class="page-head">
        <div>
            <p class="eyebrow">Moderation workflow</p>
            <h1>Manage Posts</h1>
            <p class="muted">Approve, reject, edit, or delete user-submitted lost/found records.</p>
        </div>
        <div class="form-actions">
            <a class="button ghost" href="<?= e(url('admin/categories.php')) ?>">Manage Categories</a>
            <a class="button ghost" href="<?= e(url('admin/report.php')) ?>">View Report</a>
        </div>
    </section>

    <form class="filter-bar" method="get">
        <input type="search" name="q" placeholder="Search name, description, category, color, shape, size, weight" value="<?= e($_GET['q'] ?? '') ?>">
        <select name="status_id">
            <option value="">All Statuses</option>
            <?php foreach ($statuses as $status): ?>
                <option value="<?= (int) $status['id'] ?>" <?= (string) ($_GET['status_id'] ?? '') === (string) $status['id'] ? 'selected' : '' ?>><?= e($status['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="type">
            <option value="">All Types</option>
            <option value="lost" <?= (($_GET['type'] ?? '') === 'lost') ? 'selected' : '' ?>>Lost</option>
            <option value="found" <?= (($_GET['type'] ?? '') === 'found') ? 'selected' : '' ?>>Found</option>
        </select>
        <select name="category_id">
            <option value="">All Categories</option>
            <?php foreach ($cats as $cat): ?>
                <option value="<?= (int) $cat['id'] ?>" <?= (string) ($_GET['category_id'] ?? '') === (string) $cat['id'] ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="location">
            <option value="">All Locations</option>
            <?php foreach ($locations as $location): ?>
                <option value="<?= e($location) ?>" <?= (string) ($_GET['location'] ?? '') === $location ? 'selected' : '' ?>><?= e($location) ?></option>
            <?php endforeach; ?>
        </select>
        <input type="text" name="color" placeholder="Color" value="<?= e($_GET['color'] ?? '') ?>">
        <input type="text" name="shape" placeholder="Shape" value="<?= e($_GET['shape'] ?? '') ?>">
        <input type="text" name="item_size" placeholder="Size" value="<?= e($_GET['item_size'] ?? '') ?>">
        <input type="text" name="estimated_weight" placeholder="Weight" value="<?= e($_GET['estimated_weight'] ?? '') ?>">
        <button class="button" type="submit">Filter</button>
        <a class="button ghost" href="<?= e(url('admin/posts.php')) ?>">Reset</a>
    </form>

    <div class="table-wrap">
        <table>
            <thead>
            <tr>
                <th>Item</th>
                <th>Poster</th>
                <th>Type</th>
                <th>Status</th>
                <th>Location</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><a href="<?= e(url('item.php?id=' . $item['id'])) ?>"><?= e($item['item_name']) ?></a></td>
                    <td><?= e($item['poster_name']) ?></td>
                    <td><?= e(ucfirst($item['item_type'])) ?></td>
                    <td><span class="badge <?= e(badge_class($item['status_name'])) ?>"><?= e($item['status_name']) ?></span></td>
                    <td><?= e($item['location']) ?></td>
                    <td><?= e($item['created_at']) ?></td>
                    <td class="actions">
                        <a href="<?= e(url('edit_item.php?id=' . $item['id'])) ?>">Edit</a>
                        <form method="post">
                            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                            <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                            <button name="action" value="Approved" type="submit">Approve</button>
                        </form>
                        <form method="post">
                            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                            <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                            <button name="action" value="Rejected" type="submit">Reject</button>
                        </form>
                        <form method="post" data-confirm="Delete this post permanently?">
                            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                            <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                            <button class="link-danger" name="action" value="delete" type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$items): ?>
                <tr><td colspan="7">No posts found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>
<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
