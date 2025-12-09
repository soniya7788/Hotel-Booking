<?php
// admin.php - combined admin panel (fixed syntax)
// Requires: db.php providing $pdo and (optionally) e() helper
session_start();
require_once 'db.php';

// AUTH
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

// helper
function esc($s){ return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
$uploadDir = __DIR__ . '/uploads';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
function flash_get($key) {
    if (!empty($_SESSION[$key])) { $v = $_SESSION[$key]; unset($_SESSION[$key]); return $v; }
    return '';
}
function flash_set($key, $val) { $_SESSION[$key] = $val; }

// POST handlers
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ADD HOTEL
    if (isset($_POST['action']) && $_POST['action'] === 'add_hotel') {
        $name = trim($_POST['name'] ?? '');
        $city = trim($_POST['city'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $desc = trim($_POST['description'] ?? '');
        $images_csv = '';
        if (!empty($_FILES['image']['name'])) {
            $fn = time() . '_' . preg_replace('/[^a-z0-9_\.-]/i','_', $_FILES['image']['name']);
            $target = $uploadDir . '/' . $fn;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) $images_csv = $fn;
        }
        $st = $pdo->prepare("INSERT INTO hotels (name, city, address, description, images, created_at) VALUES (?,?,?,?,?,NOW())");
        $st->execute([$name, $city, $address, $desc, $images_csv]);
        flash_set('admin_msg', "Hotel '{$name}' added.");
        header('Location: admin.php?tab=hotels'); exit;
    }

    // EDIT HOTEL
    if (isset($_POST['action']) && $_POST['action'] === 'edit_hotel') {
        $hid = intval($_POST['hotel_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $city = trim($_POST['city'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $desc = trim($_POST['description'] ?? '');
        $existing = trim($_POST['existing_images'] ?? '');
        if (!empty($_FILES['image']['name'])) {
            $fn = time() . '_' . preg_replace('/[^a-z0-9_\.-]/i','_', $_FILES['image']['name']);
            $target = $uploadDir . '/' . $fn;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                $existing = $existing ? ($existing . ',' . $fn) : $fn;
            }
        }
        $st = $pdo->prepare("UPDATE hotels SET name=?, city=?, address=?, description=?, images=? WHERE id=?");
        $st->execute([$name, $city, $address, $desc, $existing, $hid]);
        flash_set('admin_msg', "Hotel #{$hid} updated.");
        header('Location: admin.php?tab=hotels'); exit;
    }

    // DELETE HOTEL
    if (isset($_POST['action']) && $_POST['action'] === 'delete_hotel') {
        $hid = intval($_POST['hotel_id'] ?? 0);
        $st = $pdo->prepare("DELETE FROM hotels WHERE id = ?");
        $st->execute([$hid]);
        flash_set('admin_msg', "Hotel #{$hid} deleted.");
        header('Location: admin.php?tab=hotels'); exit;
    }

    // ADD ROOM
    if (isset($_POST['action']) && $_POST['action'] === 'add_room') {
        $hotel_id = intval($_POST['hotel_id'] ?? 0);
        $type = trim($_POST['type'] ?? '');
        $price = floatval($_POST['price'] ?? 0);
        $qty = intval($_POST['quantity'] ?? 1);
        $maxg = intval($_POST['max_guests'] ?? 1);
        $st = $pdo->prepare("INSERT INTO rooms (hotel_id,type,price,total_quantity,max_guests) VALUES (?,?,?,?,?)");
        $st->execute([$hotel_id, $type, $price, $qty, $maxg]);
        flash_set('admin_msg', "Room '{$type}' added for hotel #{$hotel_id}.");
        header('Location: admin.php?tab=rooms'); exit;
    }

    // EDIT ROOM
    if (isset($_POST['action']) && $_POST['action'] === 'edit_room') {
        $rid = intval($_POST['room_id'] ?? 0);
        $type = trim($_POST['type'] ?? '');
        $price = floatval($_POST['price'] ?? 0);
        $qty = intval($_POST['quantity'] ?? 1);
        $maxg = intval($_POST['max_guests'] ?? 1);
        $st = $pdo->prepare("UPDATE rooms SET type=?, price=?, total_quantity=?, max_guests=? WHERE id=?");
        $st->execute([$type, $price, $qty, $maxg, $rid]);
        flash_set('admin_msg', "Room #{$rid} updated.");
        header('Location: admin.php?tab=rooms'); exit;
    }

    // DELETE ROOM
    if (isset($_POST['action']) && $_POST['action'] === 'delete_room') {
        $rid = intval($_POST['room_id'] ?? 0);
        $st = $pdo->prepare("DELETE FROM rooms WHERE id = ?");
        $st->execute([$rid]);
        flash_set('admin_msg', "Room #{$rid} deleted.");
        header('Location: admin.php?tab=rooms'); exit;
    }

    // UPDATE BOOKING STATUS
    if (isset($_POST['action']) && $_POST['action'] === 'update_booking_status') {
        $bid = intval($_POST['booking_id'] ?? 0);
        $new = $_POST['status'] ?? 'Confirmed';
        $st = $pdo->prepare("UPDATE bookings SET status = ? WHERE id = ?");
        $st->execute([$new, $bid]);
        flash_set('admin_msg', "Booking #{$bid} status updated to {$new}.");
        header('Location: admin.php?tab=bookings'); exit;
    }

    // DELETE BOOKING
    if (isset($_POST['action']) && $_POST['action'] === 'delete_booking') {
        $bid = intval($_POST['booking_id'] ?? 0);
        $st = $pdo->prepare("DELETE FROM bookings WHERE id = ?");
        $st->execute([$bid]);
        flash_set('admin_msg', "Booking #{$bid} deleted.");
        header('Location: admin.php?tab=bookings'); exit;
    }
}

// CSV export
if (isset($_GET['export']) && $_GET['export'] === 'bookings') {
    $stmt = $pdo->query("SELECT b.id,b.user_id,b.hotel_id,b.room_id,b.checkin_date,b.checkout_date,b.guests,b.total_amount,b.status,b.created_at,
                               u.name AS user_name, u.email AS user_email, h.name AS hotel_name, r.type AS room_type
                        FROM bookings b
                        LEFT JOIN users u ON u.id = b.user_id
                        LEFT JOIN hotels h ON h.id = b.hotel_id
                        LEFT JOIN rooms r ON r.id = b.room_id
                        ORDER BY b.created_at DESC");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=bookings_export_' . date('Ymd_His') . '.csv');
    $out = fopen('php://output', 'w');
    if (!empty($rows)) fputcsv($out, array_keys($rows[0]));
    foreach ($rows as $r) fputcsv($out, $r);
    fclose($out); exit;
}

// determine tab
$tab = $_GET['tab'] ?? 'hotels';

// fetch data
$hotels = $pdo->query("SELECT * FROM hotels ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
$rooms = $pdo->query("SELECT r.*, h.name as hotel_name FROM rooms r JOIN hotels h ON h.id=r.hotel_id ORDER BY r.id DESC")->fetchAll(PDO::FETCH_ASSOC);
$bookings = $pdo->query("SELECT b.*, u.name as user_name, u.email as user_email, h.name as hotel_name, r.type as room_type
                          FROM bookings b
                          LEFT JOIN users u ON u.id=b.user_id
                          LEFT JOIN hotels h ON h.id=b.hotel_id
                          LEFT JOIN rooms r ON r.id=b.room_id
                          ORDER BY b.created_at DESC LIMIT 300")->fetchAll(PDO::FETCH_ASSOC);

$admin_msg = flash_get('admin_msg');
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin Panel — HotelBook</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root{--accent:#1a73e8;--muted:#6b7280}
    body{font-family:Inter,system-ui,-apple-system,Segoe UI,Roboto,Arial;background:#f6f8fb;color:#0f1724}
    .sidebar { width:220px; background:#fff; border-right:1px solid #eef2f6; min-height:100vh; padding:18px; position:fixed; left:0; top:0; }
    .content { margin-left:240px; padding:28px; }
    .menu-item { display:flex; align-items:center; justify-content:space-between; padding:10px 12px; border-radius:10px; color:#334155; text-decoration:none;}
    .menu-item:hover { background:#f1f5f9; color:#0f1724;}
    .menu-item.active { background:linear-gradient(90deg,var(--accent), #4dabf7); color:#fff; font-weight:600;}
    .card { border-radius:12px; box-shadow: 0 8px 24px rgba(15,23,42,0.04); }
    .small-muted { color:var(--muted); font-size:13px; }
    .table-sm td, .table-sm th { vertical-align: middle; }
    .img-thumb { width:72px; height:52px; object-fit:cover; border-radius:6px; }
    @media (max-width: 900px) { .sidebar { position:static; width:100%; display:flex; gap:8px; overflow:auto; min-height:auto; padding:12px } .content { margin-left:0; padding:16px; } }
  </style>
</head>
<body>
  <div class="d-flex">
    <!-- SIDEBAR -->
    <nav class="sidebar">
      <div class="mb-3">
        <div style="font-weight:700;font-size:18px">Admin</div>
        <div class="small-muted">HotelBook control center</div>
      </div>

      <a class="menu-item <?php if($tab==='hotels') echo 'active'; ?>" href="admin.php?tab=hotels">Hotels</a>
      <a class="menu-item <?php if($tab==='rooms') echo 'active'; ?>" href="admin.php?tab=rooms">Rooms</a>
      <a class="menu-item <?php if($tab==='bookings') echo 'active'; ?>" href="admin.php?tab=bookings">Bookings</a>
      <a class="menu-item <?php if($tab==='analytics') echo 'active'; ?>" href="admin.php?tab=analytics">Analytics</a>
      <a class="menu-item" href="admin.php?export=bookings">Export Bookings</a>

      <div style="margin-top:18px">
        <a class="btn btn-outline-secondary btn-sm w-100 mb-2" href="index.php">Open site</a>
        <a class="btn btn-danger btn-sm w-100" href="auth.php?action=logout">Logout</a>
      </div>
    </nav>

    <!-- MAIN CONTENT -->
    <main class="content">
      <?php if($admin_msg): ?>
        <div class="alert alert-success alert-dismissible fade show"><?php echo esc($admin_msg); ?><button class="btn-close" data-bs-dismiss="alert"></button></div>
      <?php endif; ?>

      <?php
      // TAB: HOTELS
      if ($tab === 'hotels'):
      ?>
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h4>Hotels</h4>
          <div>
            <a href="admin.php?tab=add_hotel" class="btn btn-primary">Add Hotel</a>
            <a href="admin.php?tab=rooms" class="btn btn-outline-secondary ms-2">Manage Rooms</a>
          </div>
        </div>

        <div class="row g-3">
          <?php foreach($hotels as $h):
            $imgs = $h['images'] ? explode(',', $h['images']) : [];
            $thumb = $imgs[0] ?? '';
          ?>
            <div class="col-12">
              <div class="card p-3 d-flex align-items-center">
                <div class="d-flex w-100 align-items-center">
                  <div style="width:72px;height:72px;flex:0 0 72px;">
                    <?php if($thumb): ?>
                      <?php if (filter_var($thumb, FILTER_VALIDATE_URL)): ?>
                        <img src="<?php echo esc($thumb); ?>" class="img-thumb" alt="">
                      <?php else: ?>
                        <img src="uploads/<?php echo esc($thumb); ?>" class="img-thumb" alt="" onerror="this.src=''">
                      <?php endif; ?>
                    <?php else: ?>
                      <div class="img-thumb" style="background:#eef2ff;display:grid;place-items:center;color:#64748b">No Image</div>
                    <?php endif; ?>
                  </div>

                  <div class="ms-3 flex-grow-1">
                    <div style="font-weight:700"><?php echo esc($h['name']); ?></div>
                    <div class="small-muted"><?php echo esc($h['city']); ?> — <?php echo esc($h['address']); ?></div>
                    <div class="mt-2 small-muted"><?php echo esc(substr($h['description'],0,180)); ?></div>
                  </div>

                  <div class="ms-3 text-end">
                    <a href="admin.php?tab=edit_hotel&hid=<?php echo $h['id']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>

                    <form method="post" style="display:inline" onsubmit="return confirm('Delete hotel <?php echo esc($h['name']); ?>?');">
                      <input type="hidden" name="action" value="delete_hotel">
                      <input type="hidden" name="hotel_id" value="<?php echo $h['id']; ?>">
                      <button class="btn btn-sm btn-danger">Delete</button>
                    </form>

                    <a href="admin.php?tab=rooms&hotel_filter=<?php echo $h['id']; ?>" class="btn btn-sm btn-outline-secondary mt-2">View Rooms</a>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

      <?php
      // TAB: add_hotel
      elseif ($tab === 'add_hotel'):
      ?>
        <h4>Add Hotel</h4>
        <div class="card p-3">
          <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="action" value="add_hotel">
            <div class="mb-2">
              <label class="form-label">Hotel name</label>
              <input name="name" class="form-control" required>
            </div>
            <div class="mb-2 row">
              <div class="col">
                <label class="form-label">City</label>
                <input name="city" class="form-control">
              </div>
              <div class="col">
                <label class="form-label">Address</label>
                <input name="address" class="form-control">
              </div>
            </div>
            <div class="mb-2">
              <label class="form-label">Description</label>
              <textarea name="description" class="form-control" rows="4"></textarea>
            </div>
            <div class="mb-2">
              <label class="form-label">Image (single)</label>
              <input type="file" name="image" class="form-control">
            </div>
            <button class="btn btn-primary">Add Hotel</button>
            <a href="admin.php?tab=hotels" class="btn btn-outline-secondary ms-2">Cancel</a>
          </form>
        </div>

      <?php
      // TAB: edit_hotel
      elseif ($tab === 'edit_hotel' && !empty($_GET['hid'])):
        $hid = intval($_GET['hid']);
        $stmt = $pdo->prepare("SELECT * FROM hotels WHERE id = ?");
        $stmt->execute([$hid]);
        $hotel = $stmt->fetch();
        if (!$hotel):
      ?>
        <div class="alert alert-danger">Hotel not found.</div>
      <?php else: 
        $imgcsv = $hotel['images'] ?? '';
      ?>
        <h4>Edit Hotel</h4>
        <div class="card p-3">
          <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="action" value="edit_hotel">
            <input type="hidden" name="hotel_id" value="<?php echo $hotel['id']; ?>">

            <div class="mb-2"><label class="form-label">Hotel name</label>
              <input name="name" class="form-control" value="<?php echo esc($hotel['name']); ?>" required></div>

            <div class="mb-2 row">
              <div class="col"><label class="form-label">City</label>
                <input name="city" class="form-control" value="<?php echo esc($hotel['city']); ?>"></div>
              <div class="col"><label class="form-label">Address</label>
                <input name="address" class="form-control" value="<?php echo esc($hotel['address']); ?>"></div>
            </div>

            <div class="mb-2"><label class="form-label">Description</label>
              <textarea name="description" class="form-control" rows="4"><?php echo esc($hotel['description']); ?></textarea></div>

            <div class="mb-2"><label class="form-label">Existing Images (comma separated)</label>
              <input name="existing_images" class="form-control" value="<?php echo esc($imgcsv); ?>"></div>

            <div class="mb-2"><label class="form-label">Upload Image (appends)</label>
              <input type="file" name="image" class="form-control"></div>

            <button class="btn btn-primary">Save Changes</button>
            <a href="admin.php?tab=hotels" class="btn btn-outline-secondary ms-2">Cancel</a>
          </form>
        </div>
      <?php endif; ?>

      <?php
      // TAB: ROOMS
      elseif ($tab === 'rooms'):
      ?>
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h4>Rooms</h4>
          <div>
            <a href="admin.php?tab=add_room" class="btn btn-primary">Add Room</a>
            <a href="admin.php?tab=hotels" class="btn btn-outline-secondary ms-2">Back to Hotels</a>
          </div>
        </div>

        <div class="card p-3 mb-3">
          <form class="row g-2 mb-3" method="get" action="admin.php">
            <input type="hidden" name="tab" value="rooms">
            <div class="col-4"><select name="hotel_filter" class="form-select">
              <option value="">Filter by hotel</option>
              <?php foreach($hotels as $h): ?>
                <option value="<?php echo $h['id']; ?>" <?php if(isset($_GET['hotel_filter']) && $_GET['hotel_filter']==$h['id']) echo 'selected'; ?>><?php echo esc($h['name']); ?></option>
              <?php endforeach; ?>
            </select></div>
            <div class="col"><button class="btn btn-outline-secondary">Filter</button></div>
          </form>

          <div class="table-responsive">
            <table class="table table-sm">
              <thead><tr><th>ID</th><th>Hotel</th><th>Type</th><th>Price</th><th>Qty</th><th>Max</th><th>Actions</th></tr></thead>
              <tbody>
                <?php
                  $filter = intval($_GET['hotel_filter'] ?? 0);
                  foreach($rooms as $r) {
                    if ($filter && $r['hotel_id'] != $filter) continue;
                ?>
                  <tr>
                    <td><?php echo $r['id']; ?></td>
                    <td><?php echo esc($r['hotel_name']); ?></td>
                    <td><?php echo esc($r['type']); ?></td>
                    <td>₹ <?php echo number_format($r['price'],2); ?></td>
                    <td><?php echo (int)$r['total_quantity']; ?></td>
                    <td><?php echo (int)$r['max_guests']; ?></td>
                    <td>
                      <a href="admin.php?tab=edit_room&rid=<?php echo $r['id']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                      <form method="post" style="display:inline" onsubmit="return confirm('Delete room #<?php echo $r['id']; ?>?');">
                        <input type="hidden" name="action" value="delete_room">
                        <input type="hidden" name="room_id" value="<?php echo $r['id']; ?>">
                        <button class="btn btn-sm btn-danger">Delete</button>
                      </form>
                    </td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>

      <?php
      // TAB: add_room
      elseif ($tab === 'add_room'):
      ?>
        <h4>Add Room</h4>
        <div class="card p-3">
          <form method="post">
            <input type="hidden" name="action" value="add_room">
            <div class="mb-2">
              <label class="form-label">Hotel</label>
              <select name="hotel_id" class="form-select" required>
                <option value="">Select hotel</option>
                <?php foreach($hotels as $h): ?><option value="<?php echo $h['id']; ?>"><?php echo esc($h['name']); ?></option><?php endforeach; ?>
              </select>
            </div>
            <div class="row g-2">
              <div class="col"><label class="form-label">Type</label><input name="type" class="form-control" required></div>
              <div class="col"><label class="form-label">Price</label><input name="price" type="number" step="0.01" class="form-control" required></div>
            </div>
            <div class="row g-2 mt-2">
              <div class="col"><label class="form-label">Quantity</label><input name="quantity" type="number" class="form-control" value="5"></div>
              <div class="col"><label class="form-label">Max guests</label><input name="max_guests" type="number" class="form-control" value="2"></div>
            </div>
            <div class="mt-3">
              <button class="btn btn-primary">Add Room</button>
              <a href="admin.php?tab=rooms" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
          </form>
        </div>

      <?php
      // TAB: edit_room
      elseif ($tab === 'edit_room' && !empty($_GET['rid'])):
        $rid = intval($_GET['rid']);
        $stmt = $pdo->prepare("SELECT r.*, h.name as hotel_name FROM rooms r JOIN hotels h ON h.id=r.hotel_id WHERE r.id=?");
        $stmt->execute([$rid]);
        $room = $stmt->fetch();
        if (!$room):
      ?>
        <div class="alert alert-danger">Room not found.</div>
      <?php else: ?>
        <h4>Edit Room #<?php echo $room['id']; ?></h4>
        <div class="card p-3">
          <form method="post">
            <input type="hidden" name="action" value="edit_room">
            <input type="hidden" name="room_id" value="<?php echo $room['id']; ?>">
            <div class="mb-2"><label class="form-label">Hotel</label><input class="form-control" value="<?php echo esc($room['hotel_name']); ?>" disabled></div>
            <div class="row g-2">
              <div class="col"><label class="form-label">Type</label><input name="type" class="form-control" value="<?php echo esc($room['type']); ?>" required></div>
              <div class="col"><label class="form-label">Price</label><input name="price" type="number" step="0.01" class="form-control" value="<?php echo esc($room['price']); ?>" required></div>
            </div>
            <div class="row g-2 mt-2">
              <div class="col"><label class="form-label">Quantity</label><input name="quantity" type="number" class="form-control" value="<?php echo esc($room['total_quantity']); ?>"></div>
              <div class="col"><label class="form-label">Max guests</label><input name="max_guests" type="number" class="form-control" value="<?php echo esc($room['max_guests']); ?>"></div>
            </div>
            <div class="mt-3"><button class="btn btn-primary">Save</button> <a href="admin.php?tab=rooms" class="btn btn-outline-secondary ms-2">Cancel</a></div>
          </form>
        </div>
      <?php endif; ?>

      <?php
      // TAB: BOOKINGS
      elseif ($tab === 'bookings'):
      ?>
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h4>Bookings</h4>
          <div>
            <a class="btn btn-outline-success" href="admin.php?export=bookings">Export CSV</a>
          </div>
        </div>

        <div class="card p-3">
          <div class="table-responsive">
            <table class="table table-sm">
              <thead><tr><th>#</th><th>User</th><th>Hotel</th><th>Room</th><th>Dates</th><th>Amount</th><th>Status</th><th>Actions</th></tr></thead>
              <tbody>
                <?php foreach($bookings as $b): ?>
                  <tr>
                    <td><?php echo $b['id']; ?></td>
                    <td><?php echo esc($b['user_name'] ?? 'Guest'); ?><div class="small-muted"><?php echo esc($b['user_email'] ?? ''); ?></div></td>
                    <td><?php echo esc($b['hotel_name']); ?></td>
                    <td><?php echo esc($b['room_type']); ?></td>
                    <td><?php echo esc($b['checkin_date']); ?> → <?php echo esc($b['checkout_date']); ?></td>
                    <td>₹ <?php echo number_format($b['total_amount'],2); ?></td>
                    <td><?php echo esc($b['status']); ?></td>
                    <td>
                      <form method="post" style="display:inline-flex; gap:6px; align-items:center;">
                        <input type="hidden" name="action" value="update_booking_status">
                        <input type="hidden" name="booking_id" value="<?php echo $b['id']; ?>">
                        <select name="status" class="form-select form-select-sm" style="width:140px">
                          <?php $opts = ['Pending','Confirmed','Cancelled','Checked-in','Checked-out']; foreach($opts as $o): ?>
                            <option <?php if($b['status']==$o) echo 'selected'; ?>><?php echo $o; ?></option>
                          <?php endforeach; ?>
                        </select>
                        <button class="btn btn-sm btn-outline-primary">Update</button>
                      </form>

                      <form method="post" style="display:inline" onsubmit="return confirm('Delete booking #<?php echo $b['id']; ?>?');">
                        <input type="hidden" name="action" value="delete_booking">
                        <input type="hidden" name="booking_id" value="<?php echo $b['id']; ?>">
                        <button class="btn btn-sm btn-danger">Delete</button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>

      <?php
      // TAB: ANALYTICS
      elseif ($tab === 'analytics'):
        $total = 0; foreach($bookings as $b) $total += floatval($b['total_amount']);
      ?>
        <h4>Analytics</h4>
        <div class="row g-3">
          <div class="col-md-4">
            <div class="card p-3">
              <div class="small-muted">Total hotels</div>
              <div style="font-weight:700;font-size:20px"><?php echo count($hotels); ?></div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card p-3">
              <div class="small-muted">Total rooms</div>
              <div style="font-weight:700;font-size:20px"><?php echo count($rooms); ?></div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card p-3">
              <div class="small-muted">Recent bookings</div>
              <div style="font-weight:700;font-size:20px"><?php echo count($bookings); ?></div>
            </div>
          </div>
        </div>
        <div class="mt-3 card p-3">
          <h6>Revenue (last 300 bookings)</h6>
          <div style="font-weight:700;font-size:18px">₹ <?php echo number_format($total,2); ?></div>
        </div>

      <?php
      // default fallback
      else:
      ?>
        <div class="alert alert-info">Select a section from the left menu.</div>
      <?php endif; // end main tab switch ?>
    </main>
  </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
