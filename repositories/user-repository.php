<?php

function findUserByEmail(PDO $pdo, string $email)
{
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function createUser(PDO $pdo, array $data)
{
    // Ambil bagian depan email sebagai username & full_name default jika form tidak menyediakan input nama
    $defaultName = explode('@', $data['email'])[0];
    $username = $data['username'] ?? $defaultName;
    $fullName = $data['full_name'] ?? ucfirst($defaultName);
    $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("
        INSERT INTO users (username, full_name, email, password, phone_number, date_of_birth, role) 
        VALUES (?, ?, ?, ?, ?, ?, 'user')
    ");

    $stmt->execute([
        $username,
        $fullName,
        $data['email'],
        $hashedPassword,
        $data['phone'] ?? null,
        $data['dob'] ?? null
    ]);

    return findUserByEmail($pdo, $data['email']);
}