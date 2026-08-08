<?php require_once __DIR__ . "/../config/auth.php"; ?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle ?? "NCC Manager") ?></title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<aside class="sidebar">
    <a class="brand" href="index.php"><span class="brand-icon">N</span><span>NCC Manager<small>Supplier system</small></span></a>
    <nav>
        <a class="<?= ($active ?? "") === "dashboard"
            ? "active"
            : "" ?>" href="index.php"><span>▦</span> Tổng quan</a>
        <a class="<?= ($active ?? "") === "suppliers"
            ? "active"
            : "" ?>" href="suppliers.php"><span>♙</span> Nhà cung cấp</a>
        <a class="<?= ($active ?? "") === "add"
            ? "active"
            : "" ?>" href="supplier_form.php"><span>＋</span> Thêm mới</a>
    </nav>
    <div class="sidebar-bottom"><div class="user-avatar">A</div><div><b><?= e(
        $_SESSION["user"]["full_name"] ?? "Admin"
    ) ?></b><small>Quản trị viên</small></div><a class="logout" href="logout.php" title="Đăng xuất">↪</a></div>
</aside>
<main class="main">
    <header class="topbar"><div><span class="breadcrumb">Hệ thống / <?= e(
        $pageTitle ?? ""
    ) ?></span><h1><?= e(
    $pageTitle ?? ""
) ?></h1></div><div class="today">◷ <?= date("d/m/Y") ?></div></header>
    <section class="content">
        <?php if (
            $message = flash("success")
        ): ?><div class="alert success">✓ <?= e(
    $message
) ?></div><?php endif; ?>
        <?php if (
            $message = flash("error")
        ): ?><div class="alert error">! <?= e($message) ?></div><?php endif; ?>
