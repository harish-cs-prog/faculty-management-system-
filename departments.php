<?php
require_once 'config.php'; require_admin(); $page_title='Departments'; verify_csrf();
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $action=$_POST['action'] ?? '';
    if($action==='add') {
        $name=trim($_POST['department_name'] ?? '');
        if($name!=='') { try { $s=db()->prepare("INSERT INTO departments(department_name) VALUES(?)"); $s->execute([$name]); flash('Department added.'); } catch(PDOException $x){ flash('Department already exists or could not be added.','danger'); } }
    } elseif($action==='delete') {
        $s=db()->prepare("DELETE FROM departments WHERE id=?"); $s->execute([(int)$_POST['id']]); flash('Department deleted. Faculty linked to it will have no department.','info');
    }
    redirect('departments.php');
}
$rows=db()->query("SELECT d.*,COUNT(u.id) faculty_count FROM departments d LEFT JOIN users u ON u.department_id=d.id AND u.role='faculty' GROUP BY d.id ORDER BY d.department_name")->fetchAll();
require 'header.php';
?>
<h1>Department management</h1><section class="panel"><h2>Add department</h2><form method="post" class="inline-form"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><input type="hidden" name="action" value="add"><input name="department_name" placeholder="Department name" required><button class="btn primary">Add department</button></form></section>
<section class="panel"><h2>Departments</h2><div class="table-wrap"><table><thead><tr><th>Department</th><th>Faculty</th><th>Action</th></tr></thead><tbody>
<?php foreach($rows as $r): ?><tr><td><?=e($r['department_name'])?></td><td><?=$r['faculty_count']?></td><td><form method="post" onsubmit="return confirm('Delete this department?')" class="inline"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?=$r['id']?>"><button class="btn danger small-btn">Delete</button></form></td></tr><?php endforeach; ?>
<?php if(!$rows): ?><tr><td colspan="3" class="empty">No departments added.</td></tr><?php endif; ?></tbody></table></div></section>
<?php require 'footer.php'; ?>
