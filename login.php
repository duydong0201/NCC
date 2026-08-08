<?php
require_once __DIR__ . "/config/auth.php";
if (!empty($_SESSION["user"])) {
    header("Location: index.php");
    exit();
}
$error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";
    // Tài khoản demo phục vụ bài tập. Khi triển khai thực tế, lưu người dùng trong CSDL và dùng password_hash().
    if ($username === "admin" && $password === "admin123") {
        $_SESSION["user"] = ["full_name" => "Nguyễn Quản Trị"];
        header("Location: index.php");
        exit();
    }
    $error = "Tên đăng nhập hoặc mật khẩu không đúng.";
}
?>
<!doctype html><html lang="vi"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Đăng nhập | NCC Manager</title><link rel="stylesheet" href="assets/style.css"></head>
<body class="login-page"><form class="login-card" method="post"><div class="login-brand"><div class="brand-icon">N</div><h1>NCC Manager</h1><p>Hệ thống quản lý nhà cung cấp</p></div><?php if (
    $error
): ?><div class="alert error"><?= e(
    $error
) ?></div><?php endif; ?><div class="field"><label>Tên đăng nhập</label><input name="username" required autofocus placeholder="Nhập tên đăng nhập"></div><div class="field" style="margin-top:16px"><label>Mật khẩu</label><input type="password" name="password" required placeholder="Nhập mật khẩu"></div><button class="btn" type="submit">Đăng nhập hệ thống →</button><p class="hint">Tài khoản demo: <b>admin</b> &nbsp;•&nbsp; Mật khẩu: <b>admin123</b></p></form></body></html>
