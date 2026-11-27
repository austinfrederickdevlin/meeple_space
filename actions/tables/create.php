<?php
require_once __DIR__ . "/../../../config/helpers.php";
require_once __DIR__ . "/../../../config/database.php";
require_once __DIR__ . "/../../../middleware/auth.php";

sessionStart();
if (($_SESSION['role'] ?? '') !== 'admin') redirect('/pages/auth/login.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("INSERT INTO cafe_tables (table_name, capacity, price_per_session) VALUES (?, ?, ?)");
        $stmt->execute([trim($_POST['table_name']), trim($_POST['capacity']), (int)$_POST['price_per_session']]);
        $_SESSION['admin_success'] = "Meja baru berhasil ditambahkan!";
    } catch (PDOException $e) {
        $_SESSION['admin_error'] = "Gagal menambah meja: " . $e->getMessage();
    }
}

redirect('/pages/admin/manage_tables.php');