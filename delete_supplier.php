<?php
require_once __DIR__ . "/config/database.php";
require_once __DIR__ . "/config/auth.php";
require_login();
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
if ($id) {
    $stmt = db()->prepare("DELETE FROM suppliers WHERE id=?");
    $stmt->execute([$id]);
    flash(
        "success",
        $stmt->rowCount()
            ? "Đã xóa nhà cung cấp."
            : "Không tìm thấy nhà cung cấp."
    );
}
header("Location: suppliers.php");
exit();
