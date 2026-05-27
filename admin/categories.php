<?php
require_once dirname(__DIR__) . '/includes/init.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = (string) ($_POST['action'] ?? '');
    $id = (int) ($_POST['id'] ?? 0);
    $name = trim((string) ($_POST['name'] ?? ''));

    try {
        if ($action === 'create') {
            if ($name === '') {
                flash('Category name is required.', 'error');
            } else {
                $stmt = db()->prepare('INSERT INTO categories (name) VALUES (?)');
                $stmt->execute([$name]);
                flash('Category added.', 'success');
            }
        } elseif ($action === 'update') {
            if ($id <= 0 || $name === '') {
                flash('Valid category name is required.', 'error');
            } else {
                $stmt = db()->prepare('UPDATE categories SET name = ? WHERE id = ?');
                $stmt->execute([$name, $id]);
                flash('Category updated.', 'success');
            }
        } elseif ($action === 'delete') {
            $stmt = db()->prepare('SELECT COUNT(*) FROM items WHERE category_id = ?');
            $stmt->execute([$id]);
            if ((int) $stmt->fetchColumn() > 0) {
                flash('This category is used by existing posts and cannot be deleted.', 'error');
            } else {
                $stmt = db()->prepare('DELETE FROM categories WHERE id = ?');
                $stmt->execute([$id]);
                flash('Category deleted.', 'success');
            }
        }
    } catch (PDOException $exception) {
        if ($exception->getCode() === '23000') {
            flash('A category with this name already exists.', 'error');
        } else {
            flash('Category action failed. Please try again.', 'error');
        }
    }

    redirect('admin/categories.php');
}

$pageTitle = 'Manage Categories';
$categories = db()->query("SELECT categories.id, categories.name, COUNT(items.id) AS item_count
    FROM categories
    LEFT JOIN items ON items.category_id = categories.id
    GROUP BY categories.id, categories.name
    ORDER BY categories.name")->fetchAll();

include dirname(__DIR__) . '/includes/header.php';
?>
<main class="page">
    <section class="page-head">
        <div>
            <p class="eyebrow">Category administration</p>
            <h1>Manage Categories</h1>
            <p class="muted">Add, rename, or remove item categories used by lost/found posts.</p>
        </div>
        <div class="form-actions">
            <a class="button ghost" href="<?= e(url('admin/posts.php')) ?>">Manage Posts</a>
            <a class="button ghost" href="<?= e(url('admin/index.php')) ?>">Dashboard</a>
        </div>
    </section>

    <form class="panel form-actions category-create-form" method="post">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="action" value="create">
        <label class="category-name-field">New Category
            <input type="text" name="name" placeholder="Electronics, Documents, Keys, Bags, Clothing" required>
        </label>
        <button class="button" type="submit">Add Category</button>
    </form>

    <div class="table-wrap">
        <table>
            <thead>
            <tr>
                <th>Category</th>
                <th>Posts</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($categories as $category): ?>
                <tr>
                    <td>
                        <form class="inline-edit-form" method="post">
                            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="id" value="<?= (int) $category['id'] ?>">
                            <input type="text" name="name" value="<?= e($category['name']) ?>" required>
                            <button class="button small" type="submit">Save</button>
                        </form>
                    </td>
                    <td><?= (int) $category['item_count'] ?></td>
                    <td class="actions">
                        <?php if ((int) $category['item_count'] === 0): ?>
                            <form method="post" data-confirm="Delete this category?">
                                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= (int) $category['id'] ?>">
                                <button class="link-danger" type="submit">Delete</button>
                            </form>
                        <?php else: ?>
                            <span class="muted">In use</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$categories): ?>
                <tr><td colspan="3">No categories found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>
<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
