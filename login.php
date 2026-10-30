<?php
require_once 'config.php';
if (current_user()) redirect('index.php');
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $stmt = db()->prepare("SELECT u.*, d.department_name FROM users u LEFT JOIN departments d ON d.id=u.department_id WHERE u.username=? AND u.active=1 LIMIT 1");
    $stmt->execute([$username]); $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = ['id'=>(int)$user['id'],'username'=>$user['username'],'full_name'=>$user['full_name'],'role'=>$user['role'],'department_id'=>$user['department_id'],'department_name'=>$user['department_name']];
        redirect('index.php');
    } else $error = 'Invalid username or password, or account is inactive.';
}
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Faculty Engagement Login</title><link rel="stylesheet" href="assets/style.css"></head>
<body class="login-bg"><main class="login-card"><div class="brand-mark">FE</div><h1>Faculty Engagement</h1><p>Sign in to continue</p>
<?php if ($error): ?><div class="alert danger"><?=e($error)?></div><?php endif; ?>
<form method="post"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>">
<label>Username</label><input name="username" autocomplete="username" required autofocus>
<label>Password</label><input type="password" name="password" autocomplete="current-password" required>
<button class="btn primary full" type="submit">Sign in</button></form>
<p class="muted small">First-time setup? <a href="install_admin.php">Create administrator</a></p></main></body></html>
