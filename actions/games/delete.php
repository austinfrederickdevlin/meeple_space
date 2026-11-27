<?php
require_once __DIR__ . "/../../../config/helpers.php";
require_once __DIR__ . "/../../../config/database.php";
require_once __DIR__ . "/../../../middleware/auth.php";

sessionStart();
if (($_SESSION['role'] ?? '') !== 'admin') redirect('/pages/auth/login.php');

if (isset($_GET['id'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM board_games WHERE id = ?");
        $stmt->execute([(int)$_GET['id']]);
        $_SESSION['admin_success'] = "Board game berhasil dihapus!";
    } catch (PDOException $e) {
        $_SESSION['admin_error'] = "Gagal menghapus game: " . $e->getMessage();
    }
}

redirect('/pages/admin/manage_games.php');