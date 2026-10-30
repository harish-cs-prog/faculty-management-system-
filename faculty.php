<?php
require_once 'config.php'; require_admin(); $page_title='Faculty'; verify_csrf();
if($_SERVER['REQUEST_METHOD']==='POST') {
    $action=$_POST['action'] ?? '';
    if($action==='add') {
        $name=trim($_POST['full_name']??''); $username=trim($_POST['username']??''); $password=$_POST['password']??''; $dept=(int)($_POST['department_id']??0);
        if($name==='' || $username==='' || strlen($password)<8 || !$dept) flash('Enter all fields and use a password of at least 8 characters.','danger');
        else {
            try { $s=db()->prepare("INSERT INTO users(username,password_hash,full_name,role,department_id) VALUES(?,?,?,'faculty',?)"); $s->execute([$username,password_hash($password,PASSWORD_DEFAULT),$name,$dept]); flash('Faculty account created.'); }
            catch(PDOException $x){ flash('Username already exists or the faculty could not be created.','danger'); }
        }
    } elseif($action==='toggle') {
        $id=(int)$_POST['id']; $s=db()->prepare("UPDATE users SET active=1-active WHERE id=? AND role='faculty'"); $s->execute([$id]); flash('Faculty account status updated.');
    }
    redirect('faculty.php');
}
$departments=db()->query("SELECT * FROM departments ORDER BY department_name")->fetchAll();
$rows=db()->query("SELECT u.id,u.full_name,u.username,u.active,d.department_name FROM users u LEFT JOIN departments d ON d.id=u.department_id WHERE u.role='faculty' ORDER BY u.full_name")->fetchAll();
require 'header.php';
?>
<h1>Faculty registration</h1>
<section class="panel"><h2>Register faculty</h2>
<?php if(!$departments): ?><div class="alert info">Add a department before registering faculty.</div><?php else: ?>
<form method="post" class="form-grid"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><input type="hidden" name="action" value="add">
<div><label>Faculty name</label><input name="full_name" required></div><div><label>Username</label><input name="username" required></div>
<div><label>Initial password</label><input type="password" name="password" minlength="8" required></div><div><label>Department</label><select name="department_id" required><option value="">Select department</option><?php foreach($departments as $d): ?><option value="<?=$d['id']?>"><?=e($d['department_name'])?></option><?php endforeach; ?></select></div>
<div class="form-actions"><button class="btn primary">Register faculty</button></div></form><?php endif; ?></section>
<section class="panel"><h2>Registered faculty</h2><div class="table-wrap"><table><thead><tr><th>Name</th><th>Username</th><th>Department</th><th>Account</th><th>Action</th></tr></thead><tbody>
<?php foreach($rows as $r): ?><tr><td><?=e($r['full_name'])?></td><td><?=e($r['username'])?></td><td><?=e($r['department_name']??'—')?></td><td><span class="pill <?=$r['active']?'good':'bad'?>"><?=$r['active']?'Active':'Inactive'?></span></td><td><form method="post" class="inline"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><input type="hidden" name="action" value="toggle"><input type="hidden" name="id" value="<?=$r['id']?>"><button class="btn outline small-btn"><?=$r['active']?'Deactivate':'Activate'?></button></form></td></tr><?php endforeach; ?>
<?php if(!$rows): ?><tr><td colspan="5" class="empty">No faculty registered.</td></tr><?php endif; ?></tbody></table></div></section>
<?php require 'footer.php'; ?>
