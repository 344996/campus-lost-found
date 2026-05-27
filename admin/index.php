<?php
require_once dirname(__DIR__) . '/includes/init.php';
require_admin();

$pageTitle = 'Admin Dashboard';
$summary = db()->query("SELECT
    COUNT(*) AS total,
    SUM(post_statuses.name = 'Pending') AS pending,
    SUM(post_statuses.name = 'Approved') AS approved,
    SUM(post_statuses.name = 'Rejected') AS rejected,
    SUM(items.item_type = 'lost') AS lost_count,
    SUM(items.item_type = 'found') AS found_count
    FROM items
    JOIN post_statuses ON post_statuses.id = items.status_id")->fetch();

include dirname(__DIR__) . '/includes/header.php';
?>
<main class="page">
    <section class="page-head">
        <div>
            <p class="eyebrow">Administrator control</p>
            <h1>Admin Dashboard</h1>
            <p class="muted">Verify posts, maintain data quality, and monitor lost/found activity.</p>
        </div>
        <div class="form-actions">
            <a class="button" href="<?= e(url('admin/posts.php')) ?>">Manage Posts</a>
            <a class="button ghost" href="<?= e(url('admin/categories.php')) ?>">Manage Categories</a>
            <a class="button ghost" href="<?= e(url('admin/report.php')) ?>">General Report</a>
        </div>
    </section>
    <section class="stats-grid">
        <div class="stat"><span><?= (int) ($summary['total'] ?? 0) ?></span><small>Total Posts</small></div>
        <div class="stat"><span><?= (int) ($summary['pending'] ?? 0) ?></span><small>Pending Review</small></div>
        <div class="stat"><span><?= (int) ($summary['approved'] ?? 0) ?></span><small>Approved</small></div>
        <div class="stat"><span><?= (int) ($summary['rejected'] ?? 0) ?></span><small>Rejected</small></div>
        <div class="stat"><span><?= (int) ($summary['lost_count'] ?? 0) ?></span><small>Lost</small></div>
        <div class="stat"><span><?= (int) ($summary['found_count'] ?? 0) ?></span><small>Found</small></div>
    </section>
</main>
<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
