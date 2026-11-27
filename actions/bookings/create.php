<?php
require_once __DIR__ . "/../../../config/helpers.php";
require_once __DIR__ . "/../../../config/database.php";
require_once __DIR__ . "/../../../middleware/auth.php";

sessionStart();
if (($_SESSION['role'] ?? '') !== 'admin') redirect('/pages/auth/login.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $tablePrice = (int)$_POST['table_price'];$taxAmount  = (int)$_POST['tax_amount'];$total      = $tablePrice +$taxAmount;
        $gameId     = !empty($_POST['board_game_id']) ? (int)$_POST['board_game_id'] : null;

        $stmt =$pdo->prepare("
            INSERT INTO bookings 
            (booking_code, table_id, board_game_id, booking_date, arrival_time, number_of_people, customer_name, phone_number, table_price, tax_amount, total_amount, payment_status, booking_status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$_POST['booking_code'],
            (int)$_POST['table_id'],$gameId,
            $_POST['booking_date'],$_POST['arrival_time'],
            (int)$_POST['number_of_people'],
            trim($_POST['customer_name']),
            trim($_POST['phone_number']),$tablePrice,
            $taxAmount,$total,
            $_POST['payment_status'],$_POST['booking_status']
        ]);

        $_SESSION['admin_success'] = "Tiket booking baru berhasil dibuat!";
    } catch (PDOException $e) {$_SESSION['admin_error'] = "Gagal membuat booking: " . $e->getMessage();
    }
}

redirect('/pages/admin/manage_bookings.php');