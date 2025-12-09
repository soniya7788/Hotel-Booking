<?php
// user.php - user dashboard with search, listing and booking modal (single-file)
session_start();
require_once 'db.php';
// --- bookings load & flash handling (paste near top of user.php) ---
$user_id = $_SESSION['user_id'] ?? 0;

// fetch bookings for this user (most recent first)
$bookingsStmt = $pdo->prepare("
  SELECT b.*, h.name AS hotel_name, r.type AS room_type
  FROM bookings b
  JOIN hotels h ON h.id = b.hotel_id
  JOIN rooms r ON r.id = b.room_id
  WHERE b.user_id = :uid
  ORDER BY b.created_at DESC
");
$bookingsStmt->execute([':uid' => $user_id]);
$user_bookings = $bookingsStmt->fetchAll(PDO::FETCH_ASSOC);

// optional flash from booking.php (session or query param)
$flash_msg = '';
if (!empty($_SESSION['flash_success'])) {
    $flash_msg = $_SESSION['flash_success'];
    unset($_SESSION['flash_success'], $_SESSION['flash_booking_id']);
} elseif (isset($_GET['booked']) && $_GET['booked'] == '1') {
    $bid = intval($_GET['id'] ?? 0);
    if ($bid) $flash_msg = "Booking confirmed — ID #{$bid}";
}

if (!empty($_SESSION['flash_success'])) {
    $msg = htmlspecialchars($_SESSION['flash_success'], ENT_QUOTES, 'UTF-8');
    $bid = intval($_SESSION['flash_booking_id'] ?? 0);
    // clear flash
    unset($_SESSION['flash_success'], $_SESSION['flash_booking_id']);

    echo <<<HTML
    <div class="container mt-3">
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>{$msg}</strong>
        <div class="mt-2">
          <a href="user.php?view=bookings" class="btn btn-sm btn-outline-success">View Bookings</a>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    </div>
HTML;
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header('Location: index.php'); exit;
}

// GET filters
$city = $_GET['city'] ?? '';
$sort = $_GET['sort'] ?? 'name';

// fetch hotels with lowest room price
$sql = "SELECT h.*, MIN(r.price) as min_price, COUNT(r.id) as room_types
        FROM hotels h JOIN rooms r ON r.hotel_id = h.id
        WHERE (:city = '' OR h.city LIKE :city_like)
        GROUP BY h.id";

$params = [':city'=>$city, ':city_like'=>"%$city%"];
if ($sort === 'price_asc') $sql .= " ORDER BY min_price ASC";
else if ($sort === 'price_desc') $sql .= " ORDER BY min_price DESC";
else $sql .= " ORDER BY h.name ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$hotels = $stmt->fetchAll();

function hotel_image($images_csv) {
    if (!$images_csv) return 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="600" height="400"><rect width="100%" height="100%" fill="%23eef2ff"/><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" fill="%238099b8" font-size="20">No Image</text></svg>';
    $parts = explode(',', $images_csv);
    $first = trim($parts[0]);
    if ($first === '') return '...';
    if (filter_var($first,FILTER_VALIDATE_URL)) return $first;
    return 'uploads/'.$first;
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"><title>User - HotelBook</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body{font-family: Poppins, sans-serif; background:#f6f8fb}
    .hero{background:#fff;padding:18px;border-radius:12px;box-shadow:0 8px 24px rgba(15,23,42,.06)}
    .listing-card{border-radius:12px;overflow:hidden;box-shadow:0 8px 20px rgba(15,23,42,.04);background:#fff}
    .btn-primary{background:linear-gradient(90deg,#2b8cff,#1a6ef0);border:none}
  </style>
</head>
<body>
<nav class="navbar navbar-white bg-white shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="#">HotelBook</a>
    <div class="ms-auto">
      <span class="me-3">Hello, <?php echo e($_SESSION['user_name'] ?? 'User'); ?></span>
      <a href="auth.php?action=logout" class="btn btn-outline-secondary btn-sm">Logout</a>
    </div>
  </div>

  
</nav>

<!-- Bookings section (paste into the main area of user.php) -->
<div id="bookingsSection" class="container my-4">
  <?php if($flash_msg): ?>
    <div class="alert alert-success alert-dismissible fade show">
      <?php echo htmlspecialchars($flash_msg, ENT_QUOTES, 'UTF-8'); ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <div class="card p-3 mb-3">
    <h5 class="mb-3">My Bookings</h5>

    <?php if (empty($user_bookings)): ?>
      <div class="text-muted">You have no bookings yet.</div>
    <?php else: ?>
      <div class="list-group">
        <?php foreach($user_bookings as $b): 
          $status = htmlspecialchars($b['status'], ENT_QUOTES, 'UTF-8');
          $bookId = (int)$b['id'];
          $isCancelable = ($status === 'Confirmed' && strtotime($b['checkin_date']) > time());
        ?>
          <div class="list-group-item d-flex justify-content-between align-items-start">
            <div>
              <div class="fw-semibold"><?php echo htmlspecialchars($b['hotel_name']); ?> — <?php echo htmlspecialchars($b['room_type']); ?></div>
              <div class="small text-muted">
                <?php echo e($b['checkin_date']); ?> → <?php echo e($b['checkout_date']); ?> · Guests: <?php echo e($b['guests']); ?>
              </div>
              <div class="small mt-1">Booking ID: #<?php echo $bookId; ?> · Amount: ₹ <?php echo number_format($b['total_amount'],2); ?></div>
            </div>

            <div class="text-end">
              <div class="mb-2"><span class="badge <?php echo $status==='Cancelled' ? 'bg-secondary' : 'bg-success'; ?>"><?php echo $status; ?></span></div>

              <?php if ($isCancelable): ?>
                <form method="post" action="booking.php" onsubmit="return confirm('Cancel this booking?');">
                  <input type="hidden" name="action" value="cancel">
                  <input type="hidden" name="booking_id" value="<?php echo $bookId; ?>">
                  <button class="btn btn-outline-danger btn-sm">Cancel</button>
                </form>
              <?php else: ?>
                <a href="hotel_detail.php?id=<?php echo (int)$b['hotel_id']; ?>" class="btn btn-outline-primary btn-sm">View Hotel</a>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</div>


<div class="container my-4">
  <div class="hero mb-4">
    <form class="row g-2 w-100">
      <div class="col-md-4"><input name="city" value="<?php echo e($city); ?>" class="form-control" placeholder="City"></div>
      <div class="col-md-3">
        <select name="sort" class="form-select">
          <option value="name" <?php if($sort==='name') echo 'selected'; ?>>Best match</option>
          <option value="price_asc" <?php if($sort==='price_asc') echo 'selected'; ?>>Lowest price first</option>
          <option value="price_desc" <?php if($sort==='price_desc') echo 'selected'; ?>>Highest price first</option>
        </select>
      </div>
      <div class="col-md-2"><button class="btn btn-primary w-100">Search</button></div>
    </form>
  </div>

  <div class="row">
    <aside class="col-lg-3 mb-4">
      <div class="card p-3"><h6>Filters</h6>
        <div class="mb-2"><label class="form-label small">Budget (min)</label><input id="minP" class="form-control form-control-sm" type="number"></div>
        <div class="mb-2"><label class="form-label small">Budget (max)</label><input id="maxP" class="form-control form-control-sm" type="number"></div>
        <button class="btn btn-outline-primary w-100" onclick="applyClientFilter()">Apply</button>
      </div>
    </aside>

    <section class="col-lg-9">
      <?php if(empty($hotels)): ?>
        <div class="alert alert-warning">No hotels found.</div>
      <?php endif; ?>

      <?php foreach($hotels as $h): ?>
        <div class="listing-card mb-4 p-0" data-price="<?php echo (float)$h['min_price']; ?>" data-name="<?php echo e($h['name']); ?>">
          <div class="row g-0">
            <div class="col-md-4"><img src="<?php echo e(hotel_image($h['images'])); ?>" alt="" style="width:100%;height:220px;object-fit:cover"></div>
            <div class="col-md-8 p-3">
              <div class="d-flex justify-content-between">
                <div>
                  <h5><?php echo e($h['name']); ?></h5>
                  <div class="small text-muted"><?php echo e($h['city']); ?> — <?php echo e($h['address']); ?></div>
                  <p class="mt-2 small"><?php echo e(substr($h['description'],0,160)); ?></p>
                </div>
                <div class="text-end">
                  <div class="h5 fw-bold">₹ <?php echo number_format($h['min_price'],2); ?></div>
                  <div class="small text-muted">per night</div>
                  <div class="mt-3">
                    <a class="btn btn-outline-secondary btn-sm" href="hotel_detail.php?id=<?php echo $h['id']; ?>">Details</a>
                    <button class="btn btn-primary btn-sm" onclick="openBook(<?php echo $h['id']; ?>,'<?php echo e($h['name']); ?>',<?php echo (float)$h['min_price']; ?>)">Book</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </section>
  </div>
</div>

<!-- booking modal -->
<div class="modal fade" id="bookModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form action="booking.php" method="post" class="modal-content">
      <input type="hidden" name="hotel_id" id="hotel_id">
      <div class="modal-header"><h5 class="modal-title">Confirm Booking</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <p id="hotel_name" class="fw-semibold"></p>
        <div class="mb-2"><label class="form-label">Check-in</label><input name="checkin_date" class="form-control" type="date" required></div>
        <div class="mb-2"><label class="form-label">Check-out</label><input name="checkout_date" class="form-control" type="date" required></div>
        <div class="mb-2"><label class="form-label">Guests</label><input name="guests" type="number" min="1" value="1" class="form-control" required></div>
      </div>
      <div class="modal-footer"><button class="btn btn-primary">Book (Pay at hotel)</button><button class="btn btn-outline-secondary" data-bs-dismiss="modal" type="button">Cancel</button></div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  function e(s){return String(s).replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/</g,'&lt;');}
  function openBook(id,name,price){
    document.getElementById('hotel_id').value = id;
    document.getElementById('hotel_name').textContent = name + ' — starting ₹' + price;
    var m = new bootstrap.Modal(document.getElementById('bookModal'));
    m.show();
  }
  function applyClientFilter(){
    var min = parseFloat(document.getElementById('minP').value) || 0;
    var max = parseFloat(document.getElementById('maxP').value) || 1e9;
    document.querySelectorAll('.listing-card').forEach(function(card){
      var p = parseFloat(card.getAttribute('data-price'))||0;
      var name = card.getAttribute('data-name').toLowerCase();
      card.style.display = (p>=min && p<=max) ? '' : 'none';
    });
  }
</script>
</body>
</html>
