<?php
require_once __DIR__ . '/includes/auth.php';

if (is_logged_in()) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($name === '' || $username === '' || $password === '') {
        $error = 'Please fill in every field.';
    } else {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
        $stmt->execute([$username]);

        if ($stmt->fetch()) {
            $error = 'That username is already taken.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO users (name, username, password_hash) VALUES (?, ?, ?)');
            $stmt->execute([$name, $username, $hash]);

            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['user_name'] = $name;

            header('Location: index.php');
            exit;
        }
    }
}

$active = '';
include __DIR__ . '/includes/header.php';
?>

<div class="auth-wrap">
  <div class="auth-card">
    <div class="auth-logo"><img src="assets/logo.png" alt="DineX"></div>
    <div class="auth-title">Create your account</div>
    <div class="auth-sub">Set up DineX for your restaurant.</div>

    <?php if ($error): ?>
      <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" action="signup.php">
      <div class="auth-field">
        <label>Restaurant / your name</label>
        <input type="text" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
      </div>
      <div class="auth-field">
        <label>Username</label>
        <input type="text" name="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
      </div>
      <div class="auth-field">
        <label>Password</label>
        <input type="password" name="password" required>
      </div>
      <button class="auth-submit" type="submit">Create account</button>
    </form>

    <div class="auth-switch">
      Already have an account? <a href="login.php">Log in</a>
    </div>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
