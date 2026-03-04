<?php
// includes/db.php
// Database connection for XAMPP (default root / no password).
// Update these values if your MySQL settings differ.

$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'appointment_db';

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($mysqli->connect_error) {
    die('DB connect error: ' . $mysqli->connect_error);
}
$mysqli->set_charset('utf8mb4');

/**
 * esc() - simple escape helper for output and basic sanitization.
 * Use when echoing form inputs or storing values.
 */
function esc($s) {
    global $mysqli;
    return htmlspecialchars($mysqli->real_escape_string(trim($s)), ENT_QUOTES, 'UTF-8');
}
