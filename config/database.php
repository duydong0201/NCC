<?php
// Cập nhật các thông số này cho phù hợp với MySQL trên máy của bạn.
const DB_HOST = "localhost";
const DB_NAME = "quan_ly_nha_cung_cap";
const DB_USER = "root";
const DB_PASS = "";

function db(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn =
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }

    return $pdo;
}
