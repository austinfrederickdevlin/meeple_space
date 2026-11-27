<?php
require_once __DIR__ . "/../../../config/helpers.php";
require_once __DIR__ . "/../../../config/database.php";
require_once __DIR__ . "/../../../middleware/auth.php";

sessionStart();
if (($_SESSION['role'] ?? '') !== 'admin') redirect('/pages/auth/login.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("
            INSERT INTO board_games (judul, gambar, pemain, durasi, kategori_utama, tingkat_kesulitan, deskripsi, is_most_wanted) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            trim($_POST['judul']),
            trim($_POST['gambar']),
            trim($_POST['pemain']),
            trim($_POST['durasi']),
            $_POST['kategori_utama'],
            $_POST['tingkat_kesulitan'],
            trim($_POST['deskripsi']),
            (int)($_POST['is_most_wanted'] ?? 0)
        ]);
        $_SESSION['admin_success'] = "Board game baru berhasil ditambahkan!";
    } catch (PDOException $e) {
        $_SESSION['admin_error'] = "Gagal menambah game: " . $e->getMessage();
    }
}

redirect('/pages/admin/manage_games.php');