<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

$userId = current_user_id();

if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = []; // product_id => quantity
}

// Handle cart actions (all via POST so nothing changes state on a plain page load)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);

    if ($action === 'add') {
        $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + 1;
    } elseif ($action === 'inc') {
        $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + 1;
    } elseif ($action === 'dec') {
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]--;
            if ($_SESSION['cart'][$id] <= 0) unset($_SESSION['cart'][$id]);
        }
    } elseif ($action === 'remove') {
        unset($_SESSION['cart'][$id]);
    } elseif ($action === 'clear') {
        $_SESSION['cart'] = [];
    }

    header('Location: billing.php');
    exit;
}

// Fetch this user's products for the picker
$stmt = $pdo->prepare('SELECT * FROM products WHERE user_id = ? ORDER BY name ASC');
$stmt->execute([$userId]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
$productsById = [];
foreach ($products as $p) $productsById[$p['id']] = $p;

// Build cart lines + totals
$lines = [];
$subtotal = 0;
$gstTotal = 0;
foreach ($_SESSION['cart'] as $pid => $qty) {
    if (!isset($productsById[$pid])) continue; // product may have been deleted
    $p = $productsById[$pid];
    $lineBase = (float)$p['price'] * $qty;
    $lineGst = $lineBase * ((float)$p['gst'] / 100);
    $subtotal += $lineBase;
    $gstTotal += $lineGst;
    $lines[] = [
        'id' => $pid,
        'name' => $p['name'],
        'price' => (float)$p['price'],
        'gst' => (float)$p['gst'],
        'qty' => $qty,
        'amount' => $lineBase + $lineGst,
    ];
}
$total = $subtotal + $gstTotal;

$active = 'billing';
include __DIR__ . '/includes/header.php';
?>

<div class="section-title">New Bill</div>
<div class="section-sub">Tap a product to add it to the current bill.</div>

<div class="bill-layout">
  <div class="card">
    <?php if (empty($products)): ?>
      <div class="empty-note">No products in inventory yet. <a href="inventory.php" style="text-decoration:underline;">Add some from the Inventory tab</a>.</div>
    <?php else: ?>
      <div class="product-pick">
        <?php foreach ($products as $p): ?>
          <div class="pick-item">
            <div>
              <div class="pi-name"><?= htmlspecialchars($p['name']) ?></div>
              <div class="pi-meta">₹<?= number_format((float)$p['price'], 2) ?> · GST <?= rtrim(rtrim(number_format((float)$p['gst'], 2), '0'), '.') ?>%</div>
            </div>
            <form method="post" action="billing.php">
              <input type="hidden" name="action" value="add">
              <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
              <button type="submit">Add</button>
            </form>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

  <div class="card">
    <div style="font-weight:700; font-size:15px; margin-bottom:4px;">Table Bill</div>

    <?php if (empty($lines)): ?>
      <div class="empty-note" style="padding:20px 0;">Bill is empty</div>
    <?php else: ?>
      <?php foreach ($lines as $line): ?>
        <div class="bill-item">
          <div class="bi-left">
            <div class="bi-name"><?= htmlspecialchars($line['name']) ?></div>
            <div class="bi-meta">₹<?= number_format($line['price'], 2) ?> × <?= $line['qty'] ?> · GST <?= rtrim(rtrim(number_format($line['gst'], 2), '0'), '.') ?>%</div>
          </div>
          <div class="qty-ctrl">
            <form method="post" action="billing.php" style="display:contents;">
              <input type="hidden" name="id" value="<?= (int)$line['id'] ?>">
              <button type="submit" name="action" value="dec">−</button>
            </form>
            <span><?= $line['qty'] ?></span>
            <form method="post" action="billing.php" style="display:contents;">
              <input type="hidden" name="id" value="<?= (int)$line['id'] ?>">
              <button type="submit" name="action" value="inc">+</button>
            </form>
          </div>
          <div class="bi-amt">₹<?= number_format($line['amount'], 2) ?></div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>

    <div class="bill-summary">
      <div class="sum-row"><span>Subtotal</span><span class="val">₹<?= number_format($subtotal, 2) ?></span></div>
      <div class="sum-row"><span>GST</span><span class="val">₹<?= number_format($gstTotal, 2) ?></span></div>
      <div class="sum-row total"><span>Total</span><span class="val">₹<?= number_format($total, 2) ?></span></div>
    </div>

    <?php if (!empty($lines)): ?>
      <a class="print-btn" href="print.php">Print bill</a>
      <form method="post" action="billing.php">
        <input type="hidden" name="action" value="clear">
        <button class="clear-btn" type="submit">Clear bill</button>
      </form>
    <?php else: ?>
      <button class="print-btn" disabled style="opacity:0.5; cursor:not-allowed;">Print bill</button>
    <?php endif; ?>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
