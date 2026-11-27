<?php
require_once __DIR__ . "/../../../config/helpers.php";
require_once __DIR__ . "/../../../config/database.php";
require_once __DIR__ . "/../../../middleware/auth.php";

sessionStart();
if (($_SESSION['role'] ?? '') !== 'admin') redirect('/pages/auth/login.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $hashed = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("
            INSERT INTO users (username, full_name, email, password, phone_number, role) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            trim($_POST['username']),
            trim($_POST['full_name']),
            trim($_POST['email']),
            $hashed,
            trim($_POST['phone_number']),
            $_POST['role']
        ]);
        $_SESSION['admin_success'] = "Akun pengguna baru berhasil dibuat!";
    } catch (PDOException $e) {
        $_SESSION['admin_error'] = "Gagal membuat akun: " . $e->getMessage();
    }
}

redirect('/pages/admin/manage_users.php');