<?php
// hotel_detail.php - shows hotel info and rooms; single-file
session_start();
require_once 'db.php';
$id = intval($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: user.php'); exit; }

$stmt = $pdo->prepare("SELECT * FROM hotels WHERE id = ?");
$stmt->execute([$id]);
$hotel = $stmt->fetch();
if (!$hotel) { header('Location: user.php'); exit; }

$stmt = $pdo->prepare("SELECT * FROM rooms WHERE hotel_id = ?");
$stmt->execute([$id]);
$rooms = $stmt->fetchAll();

function imgs($csv){
  if (!$csv) return [];
  return array_filter(array_map('trim', explode(',', $csv)));
}
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><title><?php echo e($hotel['name']); ?></title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>body{font-family:Poppins, sans-serif;background:#f6f8fb}.card{border-radius:12px}</style>
</head><body>
<nav class="navbar navbar-white bg-white shadow-sm"><div class="container"><a class="navbar-brand" href="user.php">HotelBook</a></div></nav>
<div class="container my-4">
  <div class="card p-3 mb-3">
    <div class="row">
      <div class="col-md-7">
        <?php $images = imgs($hotel['images']); if (count($images)): ?>
          <img src="<?php echo e($images[0]); ?>" style="width:100%;height:320px;object-fit:cover" onerror="this.src='data:image/svg+xml...';">
        <?php else: ?>
          <div style="height:320px;background:#eef2ff;display:flex;align-items:center;justify-content:center">No Image</div>
        <?php endif; ?>
      </div>
      <div class="col-md-5">
        <h3><?php echo e($hotel['name']); ?></h3>
        <p class="text-muted"><?php echo e($hotel['address']); ?> — <?php echo e($hotel['city']); ?></p>
        <p><?php echo e($hotel['description']); ?></p>
        <a href="user.php" class="btn btn-outline-secondary">Back</a>
      </div>
    </div>
  </div>

  <div class="card p-3">
    <h5>Rooms</h5>
    <?php foreach($rooms as $r): ?>
      <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
        <div>
          <strong><?php echo e($r['type']); ?></strong>
          <div class="small text-muted">Max guests: <?php echo e($r['max_guests']); ?></div>
        </div>
        <div class="text-end">
          <div class="h6">₹ <?php echo number_format($r['price'],2); ?></div>
          <button class="btn btn-primary btn-sm" onclick="openRoomBook(<?php echo $r['id']; ?>)">Book this room</button>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<!-- small modal for booking this room (redirects to booking.php) -->
<div class="modal fade" id="roomModal"><div class="modal-dialog"><form class="modal-content" action="booking.php" method="post">
  <input type="hidden" name="room_id" id="room_id">
  <div class="modal-header"><h5 class="modal-title">Book Room</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
  <div class="modal-body">
    <div class="mb-2"><label>Check-in</label><input name="checkin_date" required class="form-control" type="date"></div>
    <div class="mb-2"><label>Check-out</label><input name="checkout_date" required class="form-control" type="date"></div>
    <div class="mb-2"><label>Guests</label><input name="guests" type="number" min="1" class="form-control" value="1"></div>
  </div>
  <div class="modal-footer"><button class="btn btn-primary">Book</button></div>
</form></div></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  function openRoomBook(id){
    document.getElementById('room_id').value = id;
    new bootstrap.Modal(document.getElementById('roomModal')).show();
  }
</script>
</body></html>
