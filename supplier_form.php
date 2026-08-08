<?php
require_once __DIR__ . "/config/database.php";
require_once __DIR__ . "/config/auth.php";
require_login();
$pdo = db();
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT) ?: 0;
$supplier = [
    "code" => "",
    "name" => "",
    "contact_name" => "",
    "phone" => "",
    "email" => "",
    "address" => "",
    "category" => "",
    "tax_code" => "",
    "status" => "active",
];
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM suppliers WHERE id=?");
    $stmt->execute([$id]);
    $supplier = $stmt->fetch();
    if (!$supplier) {
        flash("error", "Không tìm thấy nhà cung cấp.");
        header("Location: suppliers.php");
        exit();
    }
}
$errors = [];
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    foreach (array_keys($supplier) as $field) {
        if (isset($_POST[$field])) {
            $supplier[$field] = trim($_POST[$field]);
        }
    }
    if (
        $supplier["code"] === "" ||
        $supplier["name"] === "" ||
        $supplier["contact_name"] === "" ||
        $supplier["phone"] === "" ||
        $supplier["category"] === ""
    ) {
        $errors[] = "Vui lòng điền đầy đủ các trường bắt buộc.";
    }
    if (!in_array($supplier["status"], ["active", "pause", "stop"], true)) {
        $errors[] = "Trạng thái không hợp lệ.";
    }
    if (
        $supplier["email"] !== "" &&
        !filter_var($supplier["email"], FILTER_VALIDATE_EMAIL)
    ) {
        $errors[] = "Email chưa đúng định dạng.";
    }
    if (!$errors) {
        try {
            $fields = [
                "code",
                "name",
                "contact_name",
                "phone",
                "email",
                "address",
                "category",
                "tax_code",
                "status",
            ];
            if ($id) {
                $sets = implode(",", array_map(fn($f) => "$f=?", $fields));
                $stmt = $pdo->prepare("UPDATE suppliers SET $sets WHERE id=?");
                $stmt->execute([
                    ...array_map(fn($f) => $supplier[$f] ?: null, $fields),
                    $id,
                ]);
                flash("success", "Đã cập nhật nhà cung cấp.");
            } else {
                $cols = implode(",", $fields);
                $marks = implode(",", array_fill(0, count($fields), "?"));
                $stmt = $pdo->prepare(
                    "INSERT INTO suppliers ($cols) VALUES ($marks)"
                );
                $stmt->execute(
                    array_map(fn($f) => $supplier[$f] ?: null, $fields)
                );
                flash("success", "Đã thêm nhà cung cấp mới.");
            }
            header("Location: suppliers.php");
            exit();
        } catch (PDOException $e) {
            $errors[] =
                $e->getCode() === "23000"
                    ? "Mã nhà cung cấp đã tồn tại."
                    : "Không thể lưu dữ liệu. Vui lòng thử lại.";
        }
    }
}
$pageTitle = $id ? "Cập nhật nhà cung cấp" : "Thêm nhà cung cấp";
$active = "add";
require __DIR__ . "/partials/header.php";
?>
<div class="panel form-panel"><div class="panel-header"><div><h2><?= $id
    ? "Chỉnh sửa thông tin"
    : "Tạo nhà cung cấp mới" ?></h2><p class="panel-subtitle">Các trường có dấu <span class="required">*</span> là bắt buộc</p></div></div><?php if (
    $errors
): ?><div class="alert error"><?php foreach ($errors as $error):
    e($error) ?><br><?php
endforeach; ?></div><?php endif; ?>
<form method="post"><div class="form-grid"><div class="field"><label>Mã nhà cung cấp <span class="required">*</span></label><input name="code" required maxlength="20" value="<?= e(
    $supplier["code"]
) ?>" placeholder="VD: NCC006"></div><div class="field"><label>Trạng thái <span class="required">*</span></label><select name="status"><option value="active" <?= $supplier[
    "status"
] === "active"
    ? "selected"
    : "" ?>>Đang hợp tác</option><option value="pause" <?= $supplier[
    "status"
] === "pause"
    ? "selected"
    : "" ?>>Tạm dừng</option><option value="stop" <?= $supplier["status"] ===
"stop"
    ? "selected"
    : "" ?>>Ngừng hợp tác</option></select></div><div class="field full"><label>Tên nhà cung cấp <span class="required">*</span></label><input name="name" required maxlength="150" value="<?= e(
    $supplier["name"]
) ?>" placeholder="Nhập tên công ty / cá nhân cung cấp"></div><div class="field"><label>Người liên hệ <span class="required">*</span></label><input name="contact_name" required maxlength="100" value="<?= e(
    $supplier["contact_name"]
) ?>" placeholder="Họ và tên"></div><div class="field"><label>Số điện thoại <span class="required">*</span></label><input name="phone" required maxlength="20" value="<?= e(
    $supplier["phone"]
) ?>" placeholder="VD: 0901 234 567"></div><div class="field"><label>Email</label><input type="email" name="email" maxlength="120" value="<?= e(
    $supplier["email"]
) ?>" placeholder="email@congty.vn"></div><div class="field"><label>Nhóm ngành hàng <span class="required">*</span></label><input name="category" required maxlength="100" value="<?= e(
    $supplier["category"]
) ?>" placeholder="VD: Thực phẩm"></div><div class="field"><label>Mã số thuế</label><input name="tax_code" maxlength="30" value="<?= e(
    $supplier["tax_code"]
) ?>" placeholder="Nhập mã số thuế"></div><div class="field full"><label>Địa chỉ</label><textarea name="address" maxlength="255" placeholder="Nhập địa chỉ giao dịch"><?= e(
    $supplier["address"]
) ?></textarea></div></div><div class="form-actions"><a class="btn btn-outline" href="suppliers.php">Hủy bỏ</a><button class="btn" type="submit">✓ <?= $id
    ? "Lưu thay đổi"
    : "Thêm nhà cung cấp" ?></button></div></form></div>
<?php require __DIR__ . "/partials/footer.php"; ?>
