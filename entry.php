<?php
require_once 'config.php'; require_admin(); $page_title='Daily period entry'; verify_csrf();
if($_SERVER['REQUEST_METHOD']==='POST') {
    $date=$_POST['engagement_date']??''; $period=trim($_POST['period_name']??''); $statuses=$_POST['status']??[]; $remarks=$_POST['remarks']??[];
    if(!date_valid($date) || $period==='' || !is_array($statuses)) flash('Choose a valid date, period and faculty status.','danger');
    else {
        $pdo=db(); $pdo->beginTransaction();
        try {
            $s=$pdo->prepare("INSERT INTO engagement(faculty_id,engagement_date,period_name,status,remarks,entered_by) VALUES(?,?,?,?,?,?) ON DUPLICATE KEY UPDATE status=VALUES(status),remarks=VALUES(remarks),entered_by=VALUES(entered_by)");
            $valid=$pdo->prepare("SELECT id FROM users WHERE id=? AND role='faculty' AND active=1");
            $count=0;
            foreach($statuses as $fid=>$status) {
                if(!in_array($status,['Engaged','Not Engaged'],true)) continue;
                $valid->execute([(int)$fid]); if(!$valid->fetch()) continue;
                $remark=trim((string)($remarks[$fid]??'')); $s->execute([(int)$fid,$date,$period,$status,$remark,current_user()['id']]); $count++;
            }
            $pdo->commit(); flash("$count faculty period record(s) saved. Existing records for the same faculty, date and period were updated.");
        } catch(Throwable $x) { $pdo->rollBack(); flash('Could not save entries: '.$x->getMessage(),'danger'); }
    }
    redirect('entry.php?date='.urlencode($date).'&period='.urlencode($period));
}
$date=$_GET['date']??date('Y-m-d'); if(!date_valid($date)) $date=date('Y-m-d');
$period=$_GET['period']??'Period 1';
$faculty=db()->query("SELECT u.id,u.full_name,d.department_name FROM users u LEFT JOIN departments d ON d.id=u.department_id WHERE u.role='faculty' AND u.active=1 ORDER BY d.department_name,u.full_name")->fetchAll();
$existing=db()->prepare("SELECT faculty_id,status,remarks FROM engagement WHERE engagement_date=? AND period_name=?"); $existing->execute([$date,$period]); $map=[]; foreach($existing as $x) $map[$x['faculty_id']]=$x;
$periods=['Period 1','Period 2','Period 3','Period 4','Period 5','Period 6','Period 7','Period 8'];
require 'header.php';
?>
<h1>Daily class engagement entry</h1><section class="panel">
<form method="get" class="filter-row"><div><label>Select date</label><input type="date" name="date" value="<?=e($date)?>" required></div><div><label>Period</label><select name="period"><?php foreach($periods as $p): ?><option <?=$period===$p?'selected':''?>><?=e($p)?></option><?php endforeach; ?></select></div><button class="btn outline">Load faculty</button></form>
</section>
<section class="panel"><div class="panel-head"><div><h2>Faculty status</h2><p class="muted"><?=e(date('l, d M Y',strtotime($date)))?> · <?=e($period)?></p></div><span class="pill info"><?=count($faculty)?> faculty</span></div>
<?php if(!$faculty): ?><div class="alert info">Register faculty members first.</div><?php else: ?>
<form method="post"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><input type="hidden" name="engagement_date" value="<?=e($date)?>"><input type="hidden" name="period_name" value="<?=e($period)?>">
<div class="table-wrap"><table><thead><tr><th>Faculty</th><th>Department</th><th>Engagement status</th><th>Remarks (optional)</th></tr></thead><tbody>
<?php foreach($faculty as $f): $cur=$map[$f['id']]['status']??'Not Engaged'; ?><tr><td><?=e($f['full_name'])?></td><td><?=e($f['department_name']??'—')?></td><td><select name="status[<?=$f['id']?>]"><option value="Engaged" <?=$cur==='Engaged'?'selected':''?>>Engaged</option><option value="Not Engaged" <?=$cur==='Not Engaged'?'selected':''?>>Not Engaged</option></select></td><td><input name="remarks[<?=$f['id']?>]" value="<?=e($map[$f['id']]['remarks']??'')?>" placeholder="Optional note"></td></tr><?php endforeach; ?>
</tbody></table></div><div class="form-actions"><button class="btn primary">Save engagement records</button></div></form><?php endif; ?></section>
<?php require 'footer.php'; ?>
