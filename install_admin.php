<?php
require_once 'config.php';
$message = '';
try {
    $count = (int)db()->query("SELECT COUNT(*) FROM users WHERE role='admin'")->fetchColumn();
    if ($count > 0) {
        $message = 'An administrator account already exists. For security, delete install_admin.php now.';
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        verify_csrf();
        $name = trim($_POST['full_name'] ?? 'Administrator');
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        if ($name === '' || $username === '' || strlen($password) < 8) {
            $message = 'Enter a name, username and password of at least 8 characters.';
        } else {
            $stmt = db()->prepare("INSERT INTO users(username,password_hash,full_name,role) VALUES(?,?,?,'admin')");
            $stmt->execute([$username, password_hash($password, PASSWORD_DEFAULT), $name]);
            $message = 'Administrator created. Sign in, then delete install_admin.php.';
        }
    }
} catch (Throwable $ex) { $message = 'Database error: ' . $ex->getMessage(); }
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Initial Admin Setup</title><link rel="stylesheet" href="assets/style.css"></head>
<body class="login-bg"><main class="login-card"><h1>Initial administrator setup</h1><p>Create the first administrator account.</p>
<?php if ($message): ?><div class="alert info"><?=e($message)?></div><?php endif; ?>
<form method="post"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>">
<label>Administrator name</label><input name="full_name" required>
<label>Username</label><input name="username" required>
<label>Password (minimum 8 characters)</label><input type="password" name="password" minlength="8" required>
<button class="btn primary" type="submit">Create administrator</button></form><p><a href="login.php">Go to login</a></p></main></body></html>
