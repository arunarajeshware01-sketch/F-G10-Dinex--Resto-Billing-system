<?php
// Expects $active (string: 'home' | 'inventory' | 'billing') to be set by the including page.
$active = $active ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>DineX — Billing</title>
<link rel="icon" type="image/png" href="assets/logo.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@500;600;700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div id="preloader">
  <div class="pre-logo-wrap">
    <img src="assets/logo.png" alt="DineX" class="pre-logo">
    <div class="pre-bar"></div>
  </div>
</div>

<header>
  <div class="wrap">
    <nav>
      <a href="index.php" class="logo"><img src="assets/logo.png" alt="DineX"></a>

      <?php if (is_logged_in()): ?>
        <div class="tabs">
          <a class="tab-btn <?= $active === 'home' ? 'active' : '' ?>" href="index.php">Home</a>
          <a class="tab-btn <?= $active === 'inventory' ? 'active' : '' ?>" href="inventory.php">Inventory</a>
          <a class="tab-btn <?= $active === 'billing' ? 'active' : '' ?>" href="billing.php">Billing</a>
        </div>
        <div class="nav-right">
          <span class="user-badge">Hi, <strong><?= htmlspecialchars(current_user_name()) ?></strong></span>
          <a class="logout-btn" href="logout.php">Log out</a>
        </div>
      <?php else: ?>
        <div class="nav-right">
          <a class="btn btn-line" href="login.php">Log in</a>
          <a class="btn btn-primary" href="signup.php">Sign up</a>
        </div>
      <?php endif; ?>
    </nav>
  </div>
</header>

<main>
  <div class="wrap">
