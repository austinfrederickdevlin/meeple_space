<?php
require_once __DIR__ . "/../../config/helpers.php";
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../middleware/auth.php";
require_once __DIR__ . "/../../repositories/user-repository.php";

sessionStart();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyCsrf()) {
    redirect('/pages/auth/login.php');
}

$email    = trim($_POST['email'] ?? '');
$password =$_POST['password'] ?? '';

if (empty($email) || empty($password)) {$_SESSION['auth_errors'] = ['login' => 'Email and password are required.'];
    $_SESSION['auth_old']    = ['email' =>$email];
    redirect('/pages/auth/login.php');
}

$user = findUserByEmail($pdo,$email);

// Jika email belum ada, arahkan ke form buat akun dengan email & password terisi otomatis
if (!$user) {$_SESSION['auth_errors'] = ['register' => 'Email not registered. Complete your details to create an account.'];
    $_SESSION['auth_old']    = [
        'email'    => $email,
        'password' => $password
    ];
    redirect('/pages/auth/login.php?mode=register');
}

// Cek password (mendukung hash maupun teks biasa dari seeder)
$isPasswordValid = password_verify($password,$user['password']) || ($password ===$user['password']);

if (!$isPasswordValid) {$_SESSION['auth_errors'] = ['login' => 'Wrong password. Please try again.'];
    $_SESSION['auth_old']    = ['email' =>$email];
    redirect('/pages/auth/login.php');
}

// Simpan session lengkap
session_regenerate_id(true);
$username = $user['username'] ?? explode('@', $user['email'])[0];
$role     =$user['role'] ?? 'user';

$_SESSION['user'] = [
    'id'       => $user['id'],
    'email'    => $user['email'],
    'username' => $username,
    'role'     => $role
];
$_SESSION['user_id']  =$user['id'];
$_SESSION['username'] =$username;
$_SESSION['role']     =$role;

// Arahkan sesuai role
if ($role === 'admin') {
    redirect('/pages/admin/admin_dashboard.php');
} else {
    redirect('/index.php');
}