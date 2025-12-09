<?php
// booking.php - process booking (checks availability, inserts booking)
// Accepts POST from user.php or hotel_detail.php
session_start();
require_once 'db.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header('Location: index.php'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cancel') {
    $booking_id = intval($_POST['booking_id'] ?? 0);
    if ($booking_id <= 0) {
        $_SESSION['flash_success'] = "Invalid booking.";
        header('Location: user.php?view=bookings'); exit;
    }
    // ensure booking belongs to this user
    $stmt = $pdo->prepare("SELECT * FROM bookings WHERE id = ? AND user_id = ?");
    $stmt->execute([$booking_id, $_SESSION['user_id']]);
    $bk = $stmt->fetch();
    if (!$bk) {
        $_SESSION['flash_success'] = "Booking not found.";
        header('Location: user.php?view=bookings'); exit;
    }
    // only allow cancel if not already cancelled and before checkin
    if ($bk['status'] === 'Cancelled') {
        $_SESSION['flash_success'] = "Booking already cancelled.";
        header('Location: user.php?view=bookings'); exit;
    }
    if (strtotime($bk['checkin_date']) <= time()) {
        $_SESSION['flash_success'] = "Cannot cancel past or ongoing bookings.";
        header('Location: user.php?view=bookings'); exit;
    }

    // perform cancel (simple status update). If you need refunds, handle separately.
    $u = $pdo->prepare("UPDATE bookings SET status = 'Cancelled' WHERE id = ?");
    $u->execute([$booking_id]);

    $_SESSION['flash_success'] = "Booking #{$booking_id} cancelled successfully.";
    header('Location: user.php?view=bookings');
    exit;
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header('Location: index.php'); exit;
}
$user_id = $_SESSION['user_id'];

$hotel_id = intval($_POST['hotel_id'] ?? 0);
$room_id = intval($_POST['room_id'] ?? 0);
$checkin = $_POST['checkin_date'] ?? '';
$checkout = $_POST['checkout_date'] ?? '';
$guests = intval($_POST['guests'] ?? 1);

if (!$checkin || !$checkout) {
    die('Dates required.');
}
if (strtotime($checkout) <= strtotime($checkin)) {
    die('Checkout must be after check-in.');
}

// if room_id provided: use that, else pick any room of hotel (lowest price)
if ($room_id <= 0 && $hotel_id > 0) {
    $s = $pdo->prepare("SELECT id FROM rooms WHERE hotel_id = ? ORDER BY price ASC LIMIT 1");
    $s->execute([$hotel_id]);
    $r = $s->fetch();
    if ($r) $room_id = $r['id'];
}

if ($room_id <= 0) die('Room not found.');

// get room and hotel info
$stmt = $pdo->prepare("SELECT r.*, h.name as hotel_name FROM rooms r JOIN hotels h ON h.id = r.hotel_id WHERE r.id = ?");
$stmt->execute([$room_id]);
$room = $stmt->fetch();
if (!$room) die('Room not found.');

$hotel_id = $room['hotel_id'];

// check overlapping confirmed bookings count for this room across date range
$q = "SELECT COUNT(*) as cnt FROM bookings
      WHERE room_id = :rid AND status = 'Confirmed'
      AND NOT (checkout_date <= :checkin OR checkin_date >= :checkout)";
$stmt = $pdo->prepare($q);
$stmt->execute([':rid'=>$room_id, ':checkin'=>$checkin, ':checkout'=>$checkout]);
$res = $stmt->fetch();
$booked_count = $res['cnt'] ?? 0;

if ($booked_count >= max(1,(int)$room['total_quantity'])) {
    die('No availability for selected dates.');
}

// compute nights and amount
$days = (strtotime($checkout) - strtotime($checkin)) / 86400;
if ($days < 1) $days = 1;
$total_amount = $days * (float)$room['price'];

// insert booking
$ins = $pdo->prepare("INSERT INTO bookings (user_id, hotel_id, room_id, checkin_date, checkout_date, guests, total_amount, status, created_at)
                      VALUES (:uid,:hid,:rid,:cin,:cout,:guests,:amt,'Confirmed',NOW())");
$ins->execute([
    ':uid'=>$user_id, ':hid'=>$hotel_id, ':rid'=>$room_id,
    ':cin'=>$checkin, ':cout'=>$checkout, ':guests'=>$guests, ':amt'=>$total_amount
]);

$booking_id = $pdo->lastInsertId();

// redirect to user dashboard or show success
// after successful insert
$_SESSION['flash_success'] = "Booking confirmed — your booking id is #{$booking_id}.";
$_SESSION['flash_booking_id'] = $booking_id;
header("Location: user.php");
exit;

