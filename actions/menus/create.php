<?php
require_once __DIR__ . "/../../../config/helpers.php";
require_once __DIR__ . "/../../../config/database.php";
require_once __DIR__ . "/../../../middleware/auth.php";

sessionStart();
if (($_SESSION['role'] ?? '') !== 'admin') redirect('/pages/auth/login.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("
            INSERT INTO menu_items (category_id, nama_menu, harga, gambar, deskripsi, is_favorite_home) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            (int)$_POST['category_id'],
            trim($_POST['nama_menu']),
            (int)$_POST['harga'],
            trim($_POST['gambar']),
            trim($_POST['deskripsi']),
            (int)($_POST['is_favorite_home'] ?? 0)
        ]);
        $_SESSION['admin_success'] = "Menu cafe baru berhasil ditambahkan!";
    } catch (PDOException $e) {
        $_SESSION['admin_error'] = "Gagal menambah menu: " . $e->getMessage();
    }
}

redirect('/pages/admin/manage_menus.php');