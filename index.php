<?php
require_once 'config.php'; require_login(); $page_title='Dashboard'; $me=current_user();
if ($me['role']==='admin') {
    $faculty=(int)db()->query("SELECT COUNT(*) FROM users WHERE role='faculty' AND active=1")->fetchColumn();
    $departments=(int)db()->query("SELECT COUNT(*) FROM departments")->fetchColumn();
    $today=db()->prepare("SELECT COUNT(*) total, SUM(status='Engaged') engaged FROM engagement WHERE engagement_date=?"); $today->execute([date('Y-m-d')]); $stats=$today->fetch();
    $recent=db()->query("SELECT e.engagement_date,e.period_name,e.status,u.full_name,d.department_name FROM engagement e JOIN users u ON u.id=e.faculty_id LEFT JOIN departments d ON d.id=u.department_id ORDER BY e.engagement_date DESC,e.id DESC LIMIT 8")->fetchAll();
} else {
    $q=db()->prepare("SELECT COUNT(*) total, SUM(status='Engaged') engaged FROM engagement WHERE faculty_id=?"); $q->execute([$me['id']]); $stats=$q->fetch();
    $faculty=0; $departments=0;
    $q=db()->prepare("SELECT engagement_date,period_name,status,remarks FROM engagement WHERE faculty_id=? ORDER BY engagement_date DESC,id DESC LIMIT 8"); $q->execute([$me['id']]); $recent=$q->fetchAll();
}
require 'header.php';
?>
<h1>Dashboard</h1><p class="muted">Welcome, <?=e($me['full_name'])?>.</p>
<div class="stats">
<?php if ($me['role']==='admin'): ?><div class="stat"><span>Active faculty</span><strong><?=$faculty?></strong></div><div class="stat"><span>Departments</span><strong><?=$departments?></strong></div><?php endif; ?>
<div class="stat"><span><?= $me['role']==='admin' ? "Today's entries" : "Your recorded periods" ?></span><strong><?= (int)($stats['total'] ?? 0) ?></strong></div>
<div class="stat"><span>Engaged periods</span><strong><?= (int)($stats['engaged'] ?? 0) ?></strong></div>
</div>
<section class="panel"><div class="panel-head"><h2>Recent engagement records</h2><a class="btn outline" href="reports.php">View reports</a></div>
<div class="table-wrap"><table><thead><tr><th>Date</th><?php if($me['role']==='admin'): ?><th>Faculty</th><th>Department</th><?php endif; ?><th>Period</th><th>Status</th></tr></thead><tbody>
<?php foreach($recent as $r): ?><tr><td><?=e($r['engagement_date'])?></td><?php if($me['role']==='admin'): ?><td><?=e($r['full_name'])?></td><td><?=e($r['department_name'] ?? '—')?></td><?php endif; ?><td><?=e($r['period_name'])?></td><td><span class="pill <?= $r['status']==='Engaged'?'good':'bad' ?>"><?=e($r['status'])?></span></td></tr><?php endforeach; ?>
<?php if(!$recent): ?><tr><td colspan="5" class="empty">No records available yet.</td></tr><?php endif; ?></tbody></table></div></section>
<?php if($me['role']==='admin'): ?><div class="quick-links"><a class="btn primary" href="entry.php">Enter class engagement</a><a class="btn outline" href="faculty.php">Manage faculty</a></div><?php endif; ?>
<?php require 'footer.php'; ?>
