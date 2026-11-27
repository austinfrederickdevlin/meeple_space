<?php
require_once __DIR__ . "/../../../config/helpers.php";
require_once __DIR__ . "/../../../config/database.php";
require_once __DIR__ . "/../../../middleware/auth.php";

sessionStart();
if (($_SESSION['role'] ?? '') !== 'admin') redirect('/pages/auth/login.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $id = (int)$_POST['id'];
        if (!empty($_POST['password'])) {
            $hashed = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET username = ?, full_name = ?, email = ?, password = ?, phone_number = ?, role = ? WHERE id = ?");
            $stmt->execute([trim($_POST['username']), trim($_POST['full_name']), trim($_POST['email']), $hashed, trim($_POST['phone_number']), $_POST['role'], $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET username = ?, full_name = ?, email = ?, phone_number = ?, role = ? WHERE id = ?");
            $stmt->execute([trim($_POST['username']), trim($_POST['full_name']), trim($_POST['email']), trim($_POST['phone_number']), $_POST['role'], $id]);
        }
        $_SESSION['admin_success'] = "Data pengguna berhasil diperbarui!";
    } catch (PDOException $e) {
        $_SESSION['admin_error'] = "Gagal update pengguna: " . $e->getMessage();
    }
}

redirect('/pages/admin/manage_users.php');