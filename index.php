<?php
require_once __DIR__ . '/includes/auth.php';

$active = 'home';
include __DIR__ . '/includes/header.php';
?>

<?php if (!is_logged_in()): ?>

  <div class="home-hero">
    <span class="eyebrow">Restaurant billing, simplified</span>
    <h1>Bill orders in seconds, not spreadsheets.</h1>
    <p>DineX is a lightweight billing tool for small restaurants. Add your menu once, then bill orders with tax calculated automatically — and print the receipt.</p>
    <div class="home-actions">
      <a class="btn btn-primary" href="signup.php">Create your account</a>
      <a class="btn btn-line" href="login.php">Log in</a>
    </div>
  </div>

<?php else: ?>

  <div class="home-hero">
    <span class="eyebrow">Welcome back</span>
    <h1>Bill orders in seconds, not spreadsheets.</h1>
    <p>Add your menu once, then bill orders with tax calculated automatically — and print the receipt.</p>
    <div class="home-actions">
      <a class="btn btn-primary" href="inventory.php">Manage inventory</a>
      <a class="btn btn-line" href="billing.php">Start billing</a>
    </div>
  </div>

<?php endif; ?>

<div class="home-strip">
  <div>
    <h3>1. Set up inventory</h3>
    <p>Add each item on your menu with its price and GST rate — takes a minute per item.</p>
  </div>
  <div>
    <h3>2. Bill an order</h3>
    <p>Pick items from your list, set the quantity, and the total — with tax — is calculated for you.</p>
  </div>
  <div>
    <h3>3. Print the bill</h3>
    <p>One click gives the customer a clean, itemised receipt.</p>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
