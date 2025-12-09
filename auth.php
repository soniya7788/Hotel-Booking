<?php
// auth.php
session_start();
require_once 'db.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'logout') {
    session_unset();
    session_destroy();
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

/* ---------------------------------------------------------
   REGISTRATION (user only)
--------------------------------------------------------- */
if ($action === 'register') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass = trim($_POST['password'] ?? '');

    if ($name === '' || $email === '' || $pass === '') {
        $_SESSION['flash'] = "All fields are required.";
        header('Location: index.php');
        exit;
    }

    // Check duplicate email
    $st = $pdo->prepare("SELECT id FROM users WHERE email=?");
    $st->execute([$email]);
    if ($st->fetch()) {
        $_SESSION['flash'] = "Email already registered.";
        header('Location: index.php');
        exit;
    }

    // No hashing because you requested plain password storage
    $st = $pdo->prepare("INSERT INTO users (name,email,password,role) VALUES (?,?,?, 'user')");
    $st->execute([$name, $email, $pass]);

    $_SESSION['flash'] = "Registration complete, please login.";
    header('Location: index.php');
    exit;
}

/* ---------------------------------------------------------
   LOGIN (admin hardcoded, user from DB)
--------------------------------------------------------- */
if ($action === 'login') {
    $email = trim($_POST['email'] ?? '');
    $pass  = trim($_POST['password'] ?? '');
    $role  = trim($_POST['role'] ?? 'user');

    /* ---------------------------
       1. ADMIN LOGIN (HARDCODED)
       --------------------------- */
    if ($role === 'admin') {

        // Hardcoded credentials — NO DB, NO HASH
        $admin_email = "admin@gmail.com";
        $admin_pass  = "admin123";

        if ($email === $admin_email && $pass === $admin_pass) {
            // success
            session_regenerate_id(true);
            $_SESSION['user_id'] = 0;         // dummy ID
            $_SESSION['user_name'] = "Admin";
            $_SESSION['role'] = "admin";
            header("Location: admin.php");
            exit;
        } else {
            $_SESSION['flash'] = "Invalid admin login.";
            header("Location: index.php");
            exit;
        }
    }

    /* ---------------------------
       2. USER LOGIN (FROM DB)
       --------------------------- */
    $st = $pdo->prepare("SELECT * FROM users WHERE email=? AND role='user'");
    $st->execute([$email]);
    $u = $st->fetch();

    if (!$u) {
        $_SESSION['flash'] = "User not found.";
        header("Location: index.php");
        exit;
    }

    if ($u['password'] !== $pass) { // plain comparison (no hashing)
        $_SESSION['flash'] = "Incorrect password.";
        header("Location: index.php");
        exit;
    }

    // success user login
    session_regenerate_id(true);
    $_SESSION['user_id'] = $u['id'];
    $_SESSION['user_name'] = $u['name'];
    $_SESSION['role'] = "user";

    header("Location: user.php");
    exit;
}

header("Location: index.php");
exit;
?>
