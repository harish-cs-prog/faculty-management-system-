<?php require_once 'config.php'; require_login(); $me=current_user(); ?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?=e($page_title ?? 'Faculty Engagement')?></title><link rel="stylesheet" href="assets/style.css"><script defer src="assets/app.js"></script></head><body>
<header class="topbar"><a class="brand" href="index.php"><span class="brand-mark small-mark">FE</span><span>Faculty Engagement</span></a><div class="user-area"><span><?=e($me['full_name'])?> <small>(<?=e(ucfirst($me['role']))?>)</small></span><a class="logout" href="logout.php">Logout</a></div></header>
<div class="layout"><aside class="sidebar">
<a href="index.php">▦ Dashboard</a>
<?php if ($me['role']==='admin'): ?>
<a href="departments.php">▤ Departments</a><a href="faculty.php">♙ Faculty</a><a href="entry.php">☑ Daily period entry</a>
<?php endif; ?>
<a href="reports.php">▥ Reports</a>
</aside><main class="content"><?php show_flash(); ?>
