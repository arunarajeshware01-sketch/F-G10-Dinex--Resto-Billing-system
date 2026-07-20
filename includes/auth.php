<?php
// Must be included before any HTML output on every page (uses sessions + redirects).
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/db.php';

function current_user_id() {
    return $_SESSION['user_id'] ?? null;
}

function current_user_name() {
    return $_SESSION['user_name'] ?? null;
}

function is_logged_in() {
    return current_user_id() !== null;
}

// Call at the top of any page that requires a logged-in user.
function require_login() {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}
