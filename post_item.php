<?php
require_once __DIR__ . '/includes/init.php';
require_login();

$pageTitle = 'Post Item';
$cats = categories();
$locations = location_options();
$errors = [];
$values = [
    'item_type' => 'lost',
    'category_id' => '',
    'item_name' => '',
    'color' => '',
    'shape' => '',
    'item_size' => '',
    'estimated_weight' => '',
    'location' => '',
    'date_reported' => date('Y-m-d'),
    'description' => '',
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
            $imagePath = save_uploaded_image('image');
            $stmt = db()->prepare('INSERT INTO items
                (user_id, category_id, status_id, item_type, item_name, color, shape, item_size, estimated_weight, location, date_reported, description, image_path)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([
                current_user()['id'],
                (int) $values['category_id'],
                status_id('Pending'),
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
            ]);
            flash('Your item report has been submitted for administrator verification.', 'success');
            redirect('my_posts.php');
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
            <p class="eyebrow">Structured item reporting</p>
            <h1>Post Lost / Found Item</h1>
            <p class="muted">New posts are marked Pending until an administrator verifies them.</p>
        </div>
    </section>
    <?php foreach ($errors as $error): ?><div class="notice error"><?= e($error) ?></div><?php endforeach; ?>
    <form class="panel form-grid" method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <label>Post Type
            <select name="item_type" required>
                <option value="lost" <?= $values['item_type'] === 'lost' ? 'selected' : '' ?>>Lost Item</option>
                <option value="found" <?= $values['item_type'] === 'found' ? 'selected' : '' ?>>Found Item</option>
            </select>
        </label>
        <label>Category
            <select name="category_id" required>
                <option value="">Choose category</option>
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
        <label>Image
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
            <button class="button" type="submit">Submit for Review</button>
            <a class="button ghost" href="<?= e(url('index.php')) ?>">Cancel</a>
        </div>
    </form>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>
