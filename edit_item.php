<?php
require_once __DIR__ . '/includes/init.php';
require_login();

$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
$stmt = db()->prepare(item_query_base() . ' WHERE items.id = ?');
$stmt->execute([$id]);
$item = $stmt->fetch();

if (!$item || !can_manage_item($item)) {
    flash('You are not allowed to edit that post.', 'error');
    redirect('my_posts.php');
}

$pageTitle = 'Edit Item';
$cats = categories();
$locations = location_options($item['location']);
$errors = [];
$values = [
    'item_type' => $item['item_type'],
    'category_id' => (string) $item['category_id'],
    'item_name' => $item['item_name'],
    'color' => $item['color'] ?? '',
    'shape' => $item['shape'] ?? '',
    'item_size' => $item['item_size'] ?? '',
    'estimated_weight' => $item['estimated_weight'] ?? '',
    'location' => $item['location'],
    'date_reported' => $item['date_reported'],
    'description' => $item['description'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    foreach ($values as $key => $unused) {
        $values[$key] = trim((string) ($_POST[$key] ?? ''));
    }
    if (!in_array($values['item_type'], ['lost', 'found'], true)) {
        $errors[] = 'Please select Lost or Found.';
    }
    foreach (['category_id', 'item_name', 'location', 'date_reported', 'description'] as $field) {
        if ($values[$field] === '') {
            $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
        }
    }

    if (!$errors) {
        try {
            $imagePath = $item['image_path'];
            if (!empty($_FILES['image']['name'])) {
                $imagePath = save_uploaded_image('image');
            }
            $statusId = is_admin() ? (int) $item['status_id'] : status_id('Pending');
            $stmt = db()->prepare('UPDATE items SET
                category_id = ?, status_id = ?, item_type = ?, item_name = ?, color = ?, shape = ?,
                item_size = ?, estimated_weight = ?, location = ?, date_reported = ?, description = ?, image_path = ?
                WHERE id = ?');
            $stmt->execute([
                (int) $values['category_id'],
                $statusId,
                $values['item_type'],
                $values['item_name'],
                $values['color'] ?: null,
                $values['shape'] ?: null,
                $values['item_size'] ?: null,
                $values['estimated_weight'] ?: null,
                $values['location'],
                $values['date_reported'],
                $values['description'],
                $imagePath,
                $item['id'],
            ]);
            flash(is_admin() ? 'Post updated.' : 'Post updated and sent back for administrator verification.', 'success');
            redirect(is_admin() ? 'admin/posts.php' : 'my_posts.php');
        } catch (Throwable $exception) {
            $errors[] = $exception->getMessage();
        }
    }
}

include __DIR__ . '/includes/header.php';
?>
<main class="page narrow">
    <section class="page-head">
        <div>
            <p class="eyebrow">Post management</p>
            <h1>Edit Item</h1>
            <p class="muted">User edits return to Pending status so administrators can verify the new information.</p>
        </div>
    </section>
    <?php foreach ($errors as $error): ?><div class="notice error"><?= e($error) ?></div><?php endforeach; ?>
    <form class="panel form-grid" method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
        <label>Post Type
            <select name="item_type" required>
                <option value="lost" <?= $values['item_type'] === 'lost' ? 'selected' : '' ?>>Lost Item</option>
                <option value="found" <?= $values['item_type'] === 'found' ? 'selected' : '' ?>>Found Item</option>
            </select>
        </label>
        <label>Category
            <select name="category_id" required>
                <?php foreach ($cats as $cat): ?>
                    <option value="<?= (int) $cat['id'] ?>" <?= (string) $values['category_id'] === (string) $cat['id'] ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Item Name
            <input type="text" name="item_name" value="<?= e($values['item_name']) ?>" required>
        </label>
        <label>Location
            <select name="location" required>
                <option value="">Choose location</option>
                <?php foreach ($locations as $location): ?>
                    <option value="<?= e($location) ?>" <?= $values['location'] === $location ? 'selected' : '' ?>><?= e($location) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Date Reported
            <input type="date" name="date_reported" value="<?= e($values['date_reported']) ?>" required>
        </label>
        <label>Replace Image
            <input type="file" name="image" accept="image/png,image/jpeg,image/webp,image/gif">
        </label>
        <label>Color
            <input type="text" name="color" value="<?= e($values['color']) ?>">
        </label>
        <label>Shape
            <input type="text" name="shape" value="<?= e($values['shape']) ?>">
        </label>
        <label>Size
            <input type="text" name="item_size" value="<?= e($values['item_size']) ?>">
        </label>
        <label>Estimated Weight
            <input type="text" name="estimated_weight" value="<?= e($values['estimated_weight']) ?>">
        </label>
        <label class="span-2">Description
            <textarea name="description" rows="6" required><?= e($values['description']) ?></textarea>
        </label>
        <div class="form-actions span-2">
            <button class="button" type="submit">Save Changes</button>
            <a class="button ghost" href="<?= e(is_admin() ? url('admin/posts.php') : url('my_posts.php')) ?>">Cancel</a>
        </div>
    </form>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>
