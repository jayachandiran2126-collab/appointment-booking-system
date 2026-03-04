<?php
// public/delete_booking.php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_user();

// safe session/user id
$uid = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;
$id  = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header('Location: myappointments.php');
    exit;
}

$stmt = $mysqli->prepare('DELETE FROM bookings WHERE id = ? AND user_id = ?');
if ($stmt) {
    $stmt->bind_param('ii', $id, $uid);
    $stmt->execute();
    $stmt->close();
} else {
    // dev debug: log prepare error
    error_log('delete_booking prepare failed: ' . $mysqli->error);
}

header('Location: myappointments.php');
exit;
