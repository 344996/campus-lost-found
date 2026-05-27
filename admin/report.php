<?php
require_once dirname(__DIR__) . '/includes/init.php';
require_admin();

$pageTitle = 'General Report';
$statusRows = db()->query("SELECT post_statuses.name, COUNT(items.id) AS total
    FROM post_statuses
    LEFT JOIN items ON items.status_id = post_statuses.id
    GROUP BY post_statuses.id, post_statuses.name
    ORDER BY post_statuses.id")->fetchAll();
$typeRows = db()->query("SELECT item_type, COUNT(*) AS total FROM items GROUP BY item_type")->fetchAll();
$categoryRows = db()->query("SELECT categories.name, COUNT(items.id) AS total
    FROM categories
    LEFT JOIN items ON items.category_id = categories.id
    GROUP BY categories.id, categories.name
    ORDER BY total DESC, categories.name")->fetchAll();
$recentRows = db()->query(item_query_base() . ' ORDER BY items.created_at DESC LIMIT 10')->fetchAll();
$messageCount = db()->query('SELECT COUNT(*) FROM messages')->fetchColumn();

include dirname(__DIR__) . '/includes/header.php';
?>
<main class="page">
    <section class="page-head">
        <div>
            <p class="eyebrow">System monitoring</p>
            <h1>General Report</h1>
            <p class="muted">Summary information for administrator evaluation and campus lost/found management.</p>
        </div>
        <a class="button" href="<?= e(url('admin/posts.php')) ?>">Manage Posts</a>
    </section>

    <section class="report-grid">
        <article class="panel">
            <h2>By Status</h2>
            <?php foreach ($statusRows as $row): ?>
                <div class="report-row"><span><?= e($row['name']) ?></span><strong><?= (int) $row['total'] ?></strong></div>
            <?php endforeach; ?>
        </article>
        <article class="panel">
            <h2>By Type</h2>
            <?php foreach ($typeRows as $row): ?>
                <div class="report-row"><span><?= e(ucfirst($row['item_type'])) ?></span><strong><?= (int) $row['total'] ?></strong></div>
            <?php endforeach; ?>
            <div class="report-row"><span>Contact Messages</span><strong><?= (int) $messageCount ?></strong></div>
        </article>
        <article class="panel span-2">
            <h2>By Category</h2>
            <div class="bar-list">
                <?php
                $max = max(1, ...array_map(fn ($row) => (int) $row['total'], $categoryRows));
                foreach ($categoryRows as $row):
                    $width = ((int) $row['total'] / $max) * 100;
                ?>
                    <div class="bar-row">
                        <span><?= e($row['name']) ?></span>
                        <div class="bar"><i style="width: <?= $width ?>%"></i></div>
                        <strong><?= (int) $row['total'] ?></strong>
                    </div>
                <?php endforeach; ?>
            </div>
        </article>
    </section>

    <section class="panel">
        <h2>Recent Posts</h2>
        <div class="table-wrap compact">
            <table>
                <thead><tr><th>Item</th><th>Type</th><th>Status</th><th>Category</th><th>Date</th></tr></thead>
                <tbody>
                <?php foreach ($recentRows as $item): ?>
                    <tr>
                        <td><?= e($item['item_name']) ?></td>
                        <td><?= e(ucfirst($item['item_type'])) ?></td>
                        <td><span class="badge <?= e(badge_class($item['status_name'])) ?>"><?= e($item['status_name']) ?></span></td>
                        <td><?= e($item['category_name']) ?></td>
                        <td><?= e($item['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>
<?php include dirname(__DIR__) . '/includes/footer.php'; ?>

