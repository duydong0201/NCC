<?php
require_once __DIR__ . "/config/database.php";
require_once __DIR__ . "/config/auth.php";
require_login();
$keyword = trim($_GET["q"] ?? "");
$status = $_GET["status"] ?? "";
$sql = "SELECT * FROM suppliers WHERE 1=1";
$params = [];
if ($keyword !== "") {
    $sql .=
        " AND (code LIKE ? OR name LIKE ? OR contact_name LIKE ? OR phone LIKE ?)";
    $like = "%$keyword%";
    $params = [$like, $like, $like, $like];
}
if (in_array($status, ["active", "pause", "stop"], true)) {
    $sql .= " AND status = ?";
    $params[] = $status;
}
$sql .= " ORDER BY id DESC";
$stmt = db()->prepare($sql);
$stmt->execute($params);
$suppliers = $stmt->fetchAll();
$pageTitle = "Danh sách nhà cung cấp";
$active = "suppliers";
require __DIR__ . "/partials/header.php";
$labels = [
    "active" => "Đang hợp tác",
    "pause" => "Tạm dừng",
    "stop" => "Ngừng hợp tác",
];
?>
<div class="panel"><div class="panel-header"><div><h2>Danh sách nhà cung cấp</h2><p class="panel-subtitle">Quản lý thông tin đối tác cung ứng</p></div><a class="btn" href="supplier_form.php">＋ Thêm nhà cung cấp</a></div>
<form class="filters" method="get"><input class="search" name="q" value="<?= e(
    $keyword
) ?>" placeholder="⌕  Tìm theo mã, tên, liên hệ..."><select class="search" style="width:180px" name="status"><option value="">Tất cả trạng thái</option><?php foreach (
    $labels
    as $key => $label
): ?><option value="<?= $key ?>" <?= $status === $key
    ? "selected"
    : "" ?>><?= $label ?></option><?php endforeach; ?></select><button class="btn btn-outline" type="submit">Lọc dữ liệu</button><?php if (
    $keyword ||
    $status
): ?><a class="btn btn-outline" href="suppliers.php">Xóa lọc</a><?php endif; ?></form>
<div class="table-wrap"><table><thead><tr><th>Nhà cung cấp</th><th>Người liên hệ</th><th>Điện thoại</th><th>Ngành hàng</th><th>Trạng thái</th><th></th></tr></thead><tbody><?php
if (
    !$suppliers
): ?><tr><td class="empty" colspan="6">Không tìm thấy nhà cung cấp phù hợp.</td></tr><?php endif;
foreach ($suppliers as $supplier): ?><tr><td><span class="supplier-name"><?= e(
    $supplier["name"]
) ?></span><span class="supplier-code"><?= e(
    $supplier["code"]
) ?></span></td><td><?= e($supplier["contact_name"]) ?></td><td><?= e(
    $supplier["phone"]
) ?></td><td><?= e(
    $supplier["category"]
) ?></td><td><span class="badge badge-<?= e(
    $supplier["status"]
) ?>"><?= $labels[
    $supplier["status"]
] ?></span></td><td><div class="action-links"><a class="btn btn-outline btn-small" href="supplier_form.php?id=<?= $supplier[
    "id"
] ?>">Sửa</a><a class="btn btn-danger btn-small" data-confirm="Bạn có chắc muốn xóa nhà cung cấp này?" href="delete_supplier.php?id=<?= $supplier[
    "id"
] ?>">Xóa</a></div></td></tr><?php endforeach;
?></tbody></table></div></div>
<?php require __DIR__ . "/partials/footer.php"; ?>
