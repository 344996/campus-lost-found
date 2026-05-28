<?php
require_once __DIR__ . '/includes/init.php';
require_login();

$pageTitle = 'Item List';
$cats = categories();
$locations = location_options($_GET['location'] ?? '');
[$where, $params] = build_item_filters($_GET, true);
$sql = item_query_base() . $where . ' ORDER BY items.created_at DESC LIMIT 80';
$stmt = db()->prepare($sql);
$stmt->execute($params);
$items = $stmt->fetchAll();

$summary = db()->query("SELECT
    SUM(item_type = 'lost') AS lost_count,
    SUM(item_type = 'found') AS found_count,
    COUNT(*) AS total_count
    FROM items
    JOIN post_statuses ON post_statuses.id = items.status_id
    WHERE post_statuses.name = 'Approved'")->fetch();

include __DIR__ . '/includes/header.php';
?>
<main class="page">
    <section class="page-head">
        <div>
            <p class="eyebrow">Campus recovery platform</p>
            <h1>Approved Lost and Found Items</h1>
            <p class="muted">Browse verified reports, narrow results, and contact posters through a privacy-aware message form.</p>
        </div>
        <a class="button" href="<?= e(url('post_item.php')) ?>">Post Lost/Found Item</a>
    </section>

    <section class="stats-grid">
        <div class="stat"><span><?= (int) ($summary['total_count'] ?? 0) ?></span><small>Approved Posts</small></div>
        <div class="stat"><span><?= (int) ($summary['lost_count'] ?? 0) ?></span><small>Lost Items</small></div>
        <div class="stat"><span><?= (int) ($summary['found_count'] ?? 0) ?></span><small>Found Items</small></div>
    </section>

    <form class="filter-bar" method="get">
        <input type="search" name="q" aria-label="Search keywords" placeholder="Search name, description, category, color, shape, size, weight" value="<?= e($_GET['q'] ?? '') ?>">
        <select name="type" aria-label="Filter by post type">
            <option value="">All Types</option>
            <option value="lost" <?= (($_GET['type'] ?? '') === 'lost') ? 'selected' : '' ?>>Lost</option>
            <option value="found" <?= (($_GET['type'] ?? '') === 'found') ? 'selected' : '' ?>>Found</option>
        </select>
        <select name="category_id" aria-label="Filter by category">
            <option value="">All Categories</option>
            <?php foreach ($cats as $cat): ?>
                <option value="<?= (int) $cat['id'] ?>" <?= (string) ($_GET['category_id'] ?? '') === (string) $cat['id'] ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="location" aria-label="Filter by location">
            <option value="">All Locations</option>
            <?php foreach ($locations as $location): ?>
                <option value="<?= e($location) ?>" <?= (string) ($_GET['location'] ?? '') === $location ? 'selected' : '' ?>><?= e($location) ?></option>
            <?php endforeach; ?>
        </select>
        <input type="text" name="color" aria-label="Filter by color" placeholder="Color" value="<?= e($_GET['color'] ?? '') ?>">
        <input type="text" name="shape" aria-label="Filter by shape" placeholder="Shape" value="<?= e($_GET['shape'] ?? '') ?>">
        <input type="text" name="item_size" aria-label="Filter by size" placeholder="Size" value="<?= e($_GET['item_size'] ?? '') ?>">
        <input type="text" name="estimated_weight" aria-label="Filter by estimated weight" placeholder="Weight" value="<?= e($_GET['estimated_weight'] ?? '') ?>">
        <input type="date" name="date_from" aria-label="Filter from date" value="<?= e($_GET['date_from'] ?? '') ?>">
        <input type="date" name="date_to" aria-label="Filter to date" value="<?= e($_GET['date_to'] ?? '') ?>">
        <button class="button" type="submit">Filter</button>
        <a class="button ghost" href="<?= e(url('index.php')) ?>">Reset</a>
    </form>

    <?php if (!$items): ?>
        <div class="empty-state">No approved items match your search.</div>
    <?php else: ?>
        <section class="item-grid">
            <?php foreach ($items as $item): ?>
                <article class="item-card">
                    <a class="item-image" href="<?= e(url('item.php?id=' . $item['id'])) ?>">
                        <img src="<?= e(item_image($item['image_path'])) ?>" alt="<?= e($item['item_name']) ?>">
                    </a>
                    <div class="item-body">
                        <div class="item-meta">
                            <span class="pill <?= e($item['item_type']) ?>"><?= e(ucfirst($item['item_type'])) ?></span>
                            <span><?= e($item['category_name']) ?></span>
                        </div>
                        <h2><a href="<?= e(url('item.php?id=' . $item['id'])) ?>"><?= e($item['item_name']) ?></a></h2>
                        <p><?= e(excerpt($item['description'])) ?></p>
                        <div class="item-foot">
                            <span><?= e($item['location']) ?></span>
                            <span><?= e($item['date_reported']) ?></span>
                        </div>
                        <div class="item-actions">
                            <a class="button small" href="<?= e(url('item.php?id=' . $item['id'])) ?>">View Details</a>
                            <a class="button small ghost" href="<?= e(url('item.php?id=' . $item['id'] . '#contact')) ?>">Message Poster</a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>
