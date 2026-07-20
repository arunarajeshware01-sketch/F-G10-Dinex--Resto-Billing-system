# DineX (PHP version)

A simple restaurant billing app: sign up, add products to your inventory (name, price, GST%),
then bill an order and print the receipt.

## Requirements
- PHP 7.4+ (PHP 8 recommended)
- MySQL (bundled with XAMPP) with the `pdo_mysql` extension (enabled by default in XAMPP)

## Running it with XAMPP

1. Start **Apache** and **MySQL** in the XAMPP Control Panel.
2. Copy the `dinex-php` folder into `htdocs`.
3. Visit `http://localhost/dinex-php/`.

That's it — no need to create the database by hand in phpMyAdmin. On the very first
request, `includes/db.php` connects to MySQL and runs `CREATE DATABASE IF NOT EXISTS dinex`
plus the two `CREATE TABLE IF NOT EXISTS` statements, so it sets itself up automatically.

Default connection settings (in `includes/db.php`) assume XAMPP's default MySQL — user
`root`, no password. If you've changed your MySQL root password, update `$dbUser` /
`$dbPass` at the top of that file.

You can see the resulting `dinex` database (and its `users` / `products` tables) any time
in **phpMyAdmin** at `http://localhost/phpmyadmin`.

## Deploying to real hosting (shared PHP hosting, etc.)
- Most PHP hosts (including free ones like InfinityFree) give you MySQL credentials —
  update `$host`, `$dbName`, `$dbUser`, `$dbPass` in `includes/db.php` to match what
  they give you.
- Some hosts don't allow `CREATE DATABASE` from PHP — if so, create the `dinex` database
  yourself in their control panel first, then reload the site once to create the tables.

## How it's structured
- `includes/db.php` — database connection + table creation
- `includes/auth.php` — session/login helpers
- `includes/header.php` / `includes/footer.php` — shared layout + nav
- `signup.php` / `login.php` / `logout.php` — account pages
- `index.php` — home page
- `inventory.php` — add/list/remove products (scoped to the logged-in user)
- `billing.php` — pick products into a bill (kept in the PHP session), see totals
- `print.php` — printable receipt view (use your browser's Ctrl+P / Cmd+P to print)

No JavaScript is used anywhere — every interaction (adding a product, adjusting quantity,
logging in) is a normal form submission or link, handled entirely by PHP.

## Notes on this being a demo/project
- Passwords are hashed with PHP's `password_hash()` (bcrypt) — a real improvement over
  storing plain text, though there's still no email verification, password reset, or rate
  limiting on login attempts.
- The current bill lives in the PHP session, so it's cleared when you log out or start a
  new one — this is intentional.
- Each user's inventory is private to their account (enforced via `user_id` in every query).
