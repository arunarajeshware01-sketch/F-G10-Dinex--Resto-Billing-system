<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

$userId = current_user_id();
$error = '';

// Handle add product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add') {
    $name = trim($_POST['name'] ?? '');
    $price = $_POST['price'] ?? '';
    $gst = $_POST['gst'] ?? '';

    if ($name === '' || !is_numeric($price) || (float)$price < 0) {
        $error = 'Please enter a product name and a valid price.';
    } else {
        $gstVal = is_numeric($gst) ? (float)$gst : 0;
        $stmt = $pdo->prepare('INSERT INTO products (user_id, name, price, gst) VALUES (?, ?, ?, ?)');
        $stmt->execute([$userId, $name, (float)$price, $gstVal]);
        header('Location: inventory.php');
        exit;
    }
}

// Handle remove product
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare('DELETE FROM products WHERE id = ? AND user_id = ?');
    $stmt->execute([(int)$_GET['delete'], $userId]);
    header('Location: inventory.php');
    exit;
}

// Fetch this user's products
$stmt = $pdo->prepare('SELECT * FROM products WHERE user_id = ? ORDER BY id DESC');
$stmt->execute([$userId]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$active = 'inventory';
include __DIR__ . '/includes/header.php';
?>

<div class="section-title">Inventory</div>
<div class="section-sub">Add the products your restaurant sells, with price and GST rate.</div>

<?php if ($error): ?>
  <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card">
  <form method="post" action="inventory.php">
    <input type="hidden" name="action" value="add">
    <div class="form-row">
      <div class="field">
        <label>Product name</label>
        <input type="text" name="name" placeholder="e.g. Margherita Pizza" required>
      </div>
      <div class="field">
        <label>Price (₹)</label>
        <input type="number" name="price" placeholder="249" min="0" step="0.01" required>
      </div>
      <div class="field">
        <label>GST (%)</label>
        <input type="number" name="gst" placeholder="5" min="0" step="0.01">
      </div>
      <button class="add-btn" type="submit">Add product</button>
    </div>
  </form>
</div>

<?php if (empty($products)): ?>
  <div class="empty-note">No products yet — add your first one above.</div>
<?php else: ?>
  <table>
    <thead>
      <tr>
        <th>Product</th>
        <th class="num">Price</th>
        <th class="num">GST %</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($products as $p): ?>
        <tr>
          <td><?= htmlspecialchars($p['name']) ?></td>
          <td class="num">₹<?= number_format((float)$p['price'], 2) ?></td>
          <td class="num"><?= rtrim(rtrim(number_format((float)$p['gst'], 2), '0'), '.') ?>%</td>
          <td style="text-align:right;">
            <a class="del-btn" href="inventory.php?delete=<?= (int)$p['id'] ?>">Remove</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
