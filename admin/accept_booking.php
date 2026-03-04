<?php
// admin/accept_booking.php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_admin();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    header('Location: dashboard.php');
    exit;
}

$stmt = $mysqli->prepare("UPDATE bookings SET status = 'Accepted' WHERE id = ?");
if ($stmt) {
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
} else {
    error_log('accept_booking prepare failed: ' . $mysqli->error);
}

// optional success message shown on dashboard
header('Location: dashboard.php?success=' . urlencode('Appointment accepted.'));
exit;
