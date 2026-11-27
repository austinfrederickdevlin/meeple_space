<?php
require_once __DIR__ . "/../../../config/helpers.php";
require_once __DIR__ . "/../../../config/database.php";
require_once __DIR__ . "/../../../middleware/auth.php";

sessionStart();
if (($_SESSION['role'] ?? '') !== 'admin') redirect('/pages/auth/login.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $gameId = !empty($_POST['board_game_id']) ? (int)$_POST['board_game_id'] : null;

        $stmt = $pdo->prepare("
            UPDATE bookings SET 
                customer_name = ?, phone_number = ?, table_id = ?, board_game_id = ?,
                booking_date = ?, arrival_time = ?, booking_status = ?, payment_status = ?
            WHERE id = ?
        ");
        $stmt->execute([
            trim($_POST['customer_name']),
            trim($_POST['phone_number']),
            (int)$_POST['table_id'],
            $gameId,
            $_POST['booking_date'],
            $_POST['arrival_time'],
            $_POST['booking_status'],
            $_POST['payment_status'],
            (int)$_POST['id']
        ]);

        $_SESSION['admin_success'] = "Data booking berhasil diperbarui!";
    } catch (PDOException $e) {
        $_SESSION['admin_error'] = "Gagal update booking: " . $e->getMessage();
    }
}

redirect('/pages/admin/manage_bookings.php');