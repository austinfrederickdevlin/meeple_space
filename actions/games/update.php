<?php
require_once __DIR__ . "/../../../config/helpers.php";
require_once __DIR__ . "/../../../config/database.php";
require_once __DIR__ . "/../../../middleware/auth.php";

sessionStart();
if (($_SESSION['role'] ?? '') !== 'admin') redirect('/pages/auth/login.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("
            UPDATE board_games SET 
                judul = ?, gambar = ?, pemain = ?, durasi = ?, kategori_utama = ?, tingkat_kesulitan = ?, deskripsi = ?, is_most_wanted = ?
            WHERE id = ?
        ");
        $stmt->execute([
            trim($_POST['judul']),
            trim($_POST['gambar']),
            trim($_POST['pemain']),
            trim($_POST['durasi']),
            $_POST['kategori_utama'],
            $_POST['tingkat_kesulitan'],
            trim($_POST['deskripsi']),
            (int)($_POST['is_most_wanted'] ?? 0),
            (int)$_POST['id']
        ]);
        $_SESSION['admin_success'] = "Data board game berhasil diperbarui!";
    } catch (PDOException $e) {
        $_SESSION['admin_error'] = "Gagal update game: " . $e->getMessage();
    }
}

redirect('/pages/admin/manage_games.php');