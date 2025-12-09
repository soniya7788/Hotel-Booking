<?php
// index.php - Login / Register page (single-file HTML/CSS/JS embedded)
// Redirect if already logged in
session_start();
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') header('Location: admin.php');
    else header('Location: user.php');
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>HotelBook — Sign in</title>
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <!-- Bootstrap (optional but helps layout) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Google Font -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
  <style>
    :root{
      --bg:#f6f8fb;
      --muted:#6b7280;
      --accent:#1a73e8; /* google-like blue */
      --glass: rgba(255,255,255,0.75);
      --card-radius:14px;
      --shadow: 0 10px 30px rgba(18,28,46,0.08);
    }
    html,body{height:100%}
    body{
      margin:0;
      font-family: "Inter", system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
      background: linear-gradient(180deg,#ffffff 0%, #f1f5f9 100%);
      color: #0f1724;
      -webkit-font-smoothing:antialiased;
      -moz-osx-font-smoothing:grayscale;
    }

    /* Top tiny utility bar */
    .util-bar {
      background: transparent;
      padding: 6px 16px;
      font-size: 14px;
      color: var(--muted);
      display:flex;
      justify-content: flex-end;
      gap:16px;
      align-items:center;
    }
    .util-bar a { color: var(--muted); text-decoration: none; }
    .util-bar a:hover { text-decoration: underline; color: #111827; }

    /* Main navbar */
    .main-nav {
      display:flex;
      align-items:center;
      justify-content:space-between;
      padding:18px 26px;
      background: transparent;
    }
    .brand {
      display:flex;
      align-items:center;
      gap:12px;
      font-weight:700;
      color:#0f1724;
    }
    .brand-mark {
      width:36px;height:36px;border-radius:8px;background:linear-gradient(135deg,var(--accent),#4dabf7);
      display:grid;place-items:center;color:#fff;font-weight:700;font-size:18px;
      box-shadow: 0 6px 18px rgba(26,115,232,0.18);
    }

    /* Hero */
    .hero {
      max-width:1100px;
      margin: 32px auto 72px;
      padding: 34px;
      display:grid;
      grid-template-columns: 1fr 420px;
      gap: 28px;
      align-items:center;
      width: calc(100% - 40px);
    }
    .hero-left {
      padding:28px;
    }
    .kicker { color:var(--muted); font-weight:600; margin-bottom:10px; font-size:14px; }
    h1 { font-size:32px; margin:0 0 12px; line-height:1.05; }
    p.lead { color:var(--muted); font-size:15px; margin-bottom:22px; }

    /* Floating card (form) */
    .card-float {
      background: var(--glass);
      backdrop-filter: blur(6px);
      border-radius: var(--card-radius);
      box-shadow: var(--shadow);
      padding:22px;
    }

    .role-toggle {
      display:flex;
      gap:8px;
      background: rgba(15,23,42,0.03);
      padding:6px;
      border-radius:10px;
      width:max-content;
    }
    .role-btn {
      padding:8px 14px;
      border-radius:8px;
      cursor:pointer;
      font-weight:600;
      font-size:14px;
      color:var(--muted);
      border: none;
      background: transparent;
    }
    .role-btn.active {
      background: linear-gradient(90deg,var(--accent), #4dabf7);
      color: #fff;
      box-shadow: 0 6px 18px rgba(26,115,232,0.14);
    }

    .form-label { font-size:13px; color:#374151; font-weight:600; }
    .small-note { font-size:13px; color:var(--muted); margin-top:8px; }

    .alt-actions { margin-top:12px; display:flex; gap:10px; justify-content:space-between; align-items:center; }

    /* footer note */
    .foot-note { text-align:center; color:var(--muted); margin-top:36px; font-size:13px; }

    /* Responsive */
    @media (max-width: 980px) {
      .hero { grid-template-columns: 1fr; padding:18px; gap:18px; }
      .hero-right { order:-1; }
    }

    /* micro animations */
    .btn-brand {
      background: linear-gradient(90deg,var(--accent), #4dabf7);
      border: none; color:#fff; padding:10px 16px; border-radius:10px; font-weight:600;
      box-shadow: 0 8px 30px rgba(26,115,232,0.12);
    }
    input.form-control, select.form-select {
      border-radius:10px; padding:12px 14px; border:1px solid rgba(15,23,42,0.06);
      box-shadow: inset 0 -1px 0 rgba(255,255,255,0.5);
    }
    .link-plain { color:var(--accent); text-decoration:none; font-weight:600; }
    .link-plain:hover { text-decoration: underline; }

    /* small brand utilities */
    .ghost { color:var(--muted); font-size:13px; }
  </style>
</head>
<body>

  <!-- tiny util bar -->
  <div class="util-bar">
    <a href="#" class="ghost">Help</a>
    <a href="#" class="ghost">Privacy</a>
    <a href="#" class="ghost">Terms</a>
  </div>

  <!-- main nav -->
  <header class="main-nav container">
    <div class="brand">
      <div class="brand-mark">HB</div>
      <div>
        <div style="font-size:14px;color:#0f1724">HotelBook</div>
        <div style="font-size:12px;color:var(--muted);margin-top:-2px">Simple hotel booking demo</div>
      </div>
    </div>

    <!-- right: user/admin quick links -->
    <div class="d-flex align-items-center gap-3">
      <nav class="d-none d-md-flex align-items-center gap-2">
        <a class="ghost" href="#">Destinations</a>
        <a class="ghost" href="#">Collections</a>
        <a class="ghost" href="#">Contact</a>
      </nav>
      <div>
        <a href="index.php" class="link-plain">Sign in</a>
      </div>
    </div>
  </header>

  <!-- hero with left explanation and right sign-in card -->
  <section class="hero container">
    <div class="hero-left">
      <div class="kicker">Welcome back</div>
      <h1>Book comfortable stays across Maharashtra</h1>
      <p class="lead">Fast, reliable bookings for short & long stays. Login as a <strong>user</strong> to search & book, or as <strong>admin</strong> to manage hotels and bookings.</p>

      <div style="display:flex;gap:12px;align-items:center;margin-top:14px;">
        <div style="display:flex;align-items:center;gap:12px;">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" aria-hidden>
            <rect x="1" y="3" width="22" height="14" rx="2" stroke="#c7d2fe" stroke-width="1.4" fill="#eef2ff"/>
            <path d="M3 10h18" stroke="#c7d2fe" stroke-width="1.4" />
          </svg>
          <div>
            <div style="font-weight:700">Secure & simple</div>
            <div class="small-note">Your data is protected. We only store minimal info for demo.</div>
          </div>
        </div>

        <div style="display:flex;align-items:center;gap:12px;margin-left:20px">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" aria-hidden>
            <circle cx="12" cy="12" r="9" stroke="#c7d2fe" stroke-width="1.4" fill="#f0f9ff"/>
            <path d="M8 12h8" stroke="#7c3aed" stroke-width="1.6" stroke-linecap="round" />
          </svg>
          <div>
            <div style="font-weight:700">Pay at hotel</div>
            <div class="small-note">MVP: No online payments required.</div>
          </div>
        </div>
      </div>

      <div class="foot-note">Use the sample accounts on the right to login or create a new user account for testing.</div>
    </div>

    <aside class="hero-right">
      <div class="card-float">
        <!-- role selector -->
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px">
          <div class="role-toggle" role="tablist" aria-label="Role switch">
            <button id="btn-user" class="role-btn active" aria-pressed="true" onclick="switchRole('user')">User</button>
            <button id="btn-admin" class="role-btn" aria-pressed="false" onclick="switchRole('admin')">Admin</button>
          </div>

          <div style="font-size:13px;color:var(--muted)">Demo</div>
        </div>

        <!-- elegant login form -->
        <form id="loginForm" method="post" action="auth.php" novalidate>
          <input type="hidden" name="action" value="login" />
          <input type="hidden" id="roleInput" name="role" value="user" />
          <div class="mb-2">
            <label class="form-label">Email</label>
            <input required name="email" id="email" type="email" class="form-control" placeholder="you@example.com" />
          </div>

          <div class="mb-2">
            <label class="form-label">Password</label>
            <input required name="password" id="password" type="password" class="form-control" placeholder="Password" />
          </div>

          <div class="alt-actions">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="remember">
              <label class="form-check-label small" for="remember">Remember me</label>
            </div>
            <a href="#" class="small ghost">Forgot?</a>
          </div>

          <div style="margin-top:14px;display:flex;gap:10px;">
            <button type="submit" class="btn-brand w-100">Sign in</button>
            <button type="button" class="btn btn-outline-secondary w-100" id="quickFill">Quick fill</button>
          </div>

          <div style="margin-top:12px;text-align:center" class="small-note">Or register a <a href="#register" class="link-plain" onclick="showRegister(event)">new user</a></div>
        </form>

        <!-- small register form hidden by default -->
        <form id="registerForm" method="post" action="auth.php" style="display:none;margin-top:12px" novalidate>
          <input type="hidden" name="action" value="register" />
          <div class="mb-2">
            <label class="form-label">Full name</label>
            <input name="name" type="text" class="form-control" placeholder="Your full name" />
          </div>
          <div class="mb-2">
            <label class="form-label">Email</label>
            <input name="email" type="email" class="form-control" placeholder="you@example.com" />
          </div>
          <div class="mb-2">
            <label class="form-label">Password</label>
            <input name="password" type="password" class="form-control" placeholder="Choose a password" />
          </div>
          <div style="display:flex;gap:10px;margin-top:8px">
            <button class="btn btn-brand w-100">Create account</button>
            <button type="button" class="btn btn-outline-secondary w-100" onclick="cancelRegister()">Cancel</button>
          </div>
        </form>

      </div>
    </aside>
  </section>

  <footer style="padding:18px 0;background:transparent">
    <div class="container text-center small" style="color:var(--muted)">
      © <?php echo date('Y'); ?> HotelBook — made for demo & testing
    </div>
  </footer>

  <!-- JS (Bootstrap + small custom) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // role toggle logic
    function switchRole(role) {
      var userBtn = document.getElementById('btn-user');
      var adminBtn = document.getElementById('btn-admin');
      var roleInput = document.getElementById('roleInput');

      if (role === 'admin') {
        userBtn.classList.remove('active');
        userBtn.setAttribute('aria-pressed','false');
        adminBtn.classList.add('active');
        adminBtn.setAttribute('aria-pressed','true');
        roleInput.value = 'admin';
      } else {
        adminBtn.classList.remove('active');
        adminBtn.setAttribute('aria-pressed','false');
        userBtn.classList.add('active');
        userBtn.setAttribute('aria-pressed','true');
        roleInput.value = 'user';
      }

      // clear any visible register form
      cancelRegister();
    }

    // Quick fill sample credentials for demo (toggles based on role)
    document.getElementById('quickFill').addEventListener('click', function(){
      var role = document.getElementById('roleInput').value;
      if (role === 'admin') {
        document.getElementById('email').value = 'admin@example.com';
        document.getElementById('password').value = 'admin123';
      } else {
        document.getElementById('email').value = 'user1@example.com';
        document.getElementById('password').value = 'password123';
      }
      document.getElementById('email').focus();
    });

    // register toggle
    function showRegister(e){
      if (e) e.preventDefault();
      document.getElementById('loginForm').style.display = 'none';
      document.getElementById('registerForm').style.display = 'block';
    }
    function cancelRegister(){
      document.getElementById('registerForm').style.display = 'none';
      document.getElementById('loginForm').style.display = 'block';
    }

    // small progressive enhancement: allow Enter key in register to submit via login form too
    // (no-op here — left intentionally for UX)
  </script>
</body>
</html>

