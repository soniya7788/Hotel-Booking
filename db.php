<?php
// db.php - single small server-only file (keep it secure)
// Edit these values to match your MySQL setup
$DB_HOST = 'localhost';
$DB_NAME = 'hotel_booking';
$DB_USER = 'root';
$DB_PASS = '';

// PDO connection
try {
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (Exception $e) {
    die("DB connection failed: " . $e->getMessage());
}

// simple escape helper for output
function e($s) {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}
