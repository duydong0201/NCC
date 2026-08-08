<?php
session_start();

function require_login(): void
{
    if (empty($_SESSION["user"])) {
        header("Location: login.php");
        exit();
    }
}

function e(?string $value): string
{
    return htmlspecialchars($value ?? "", ENT_QUOTES, "UTF-8");
}

function flash(string $key, ?string $value = null): ?string
{
    if ($value !== null) {
        $_SESSION["flash"][$key] = $value;
        return null;
    }
    $message = $_SESSION["flash"][$key] ?? null;
    unset($_SESSION["flash"][$key]);
    return $message;
}
