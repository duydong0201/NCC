<?php
require_once __DIR__ . "/config/database.php";
require_once __DIR__ . "/config/auth.php";
require_login();
$pdo = db();
$total = (int) $pdo->query("SELECT COUNT(*) FROM suppliers")->fetchColumn();
$activeCount = (int) $pdo
    ->query("SELECT COUNT(*) FROM suppliers WHERE status = 'active'")
    ->fetchColumn();
$categories = (int) $pdo
    ->query("SELECT COUNT(DISTINCT category) FROM suppliers")
    ->fetchColumn();
$recent = $pdo
    ->query("SELECT * FROM suppliers ORDER BY created_at DESC LIMIT 5")
    ->fetchAll();
$byCategory = $pdo
    ->query(
        "SELECT category, COUNT(*) total FROM suppliers GROUP BY category ORDER BY total DESC LIMIT 5"
    )
    ->fetchAll();
$pageTitle = "Tổng quan";
$active = "dashboard";
require __DIR__ . "/partials/header.php";
?>
<div class="grid-stats"><div class="stat-card"><div class="stat-icon">♙</div><div><div class="stat-label">Tổng nhà cung cấp</div><div class="stat-value"><?= $total ?></div></div></div><div class="stat-card"><div class="stat-icon">✓</div><div><div class="stat-label">Đang hợp tác</div><div class="stat-value"><?= $activeCount ?></div></div></div><div class="stat-card"><div class="stat-icon">▤</div><div><div class="stat-label">Nhóm ngành hàng</div><div class="stat-value"><?= $categories ?></div></div></div><div class="stat-card"><div class="stat-icon">⌁</div><div><div class="stat-label">Tạm ngừng / kết thúc</div><div class="stat-value"><?= $total -
    $activeCount ?></div></div></div></div>
<div class="report-grid"><div class="panel"><div class="panel-header"><div><h2>Nhà cung cấp mới cập nhật</h2><p class="panel-subtitle">Danh sách 5 đối tác gần nhất</p></div><a class="btn btn-outline btn-small" href="suppliers.php">Xem tất cả</a></div><div class="table-wrap"><table><thead><tr><th>Nhà cung cấp</th><th>Người liên hệ</th><th>Ngành hàng</th><th>Trạng thái</th></tr></thead><tbody><?php foreach (
    $recent
    as $supplier
): ?><tr><td><span class="supplier-name"><?= e(
    $supplier["name"]
) ?></span><span class="supplier-code"><?= e(
    $supplier["code"]
) ?></span></td><td><?= e($supplier["contact_name"]) ?></td><td><?= e(
    $supplier["category"]
) ?></td><td><?php $labels = [
    "active" => "Đang hợp tác",
    "pause" => "Tạm dừng",
    "stop" => "Ngừng hợp tác",
]; ?><span class="badge badge-<?= e($supplier["status"]) ?>"><?= $labels[
    $supplier["status"]
] ?></span></td></tr><?php endforeach; ?></tbody></table></div></div><div class="panel"><div class="panel-header"><div><h2>Phân bố ngành hàng</h2><p class="panel-subtitle">Theo số lượng đối tác</p></div></div><?php foreach (
    $byCategory
    as $row
): ?><div class="category-row"><span><?= e(
    $row["category"]
) ?></span><div class="bar"><i style="width:<?= $total
    ? round(($row["total"] / $total) * 100)
    : 0 ?>%"></i></div><b><?= $row[
    "total"
] ?></b></div><?php endforeach; ?></div></div>
<?php require __DIR__ . "/partials/footer.php"; ?>
