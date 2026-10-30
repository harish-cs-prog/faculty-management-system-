<?php
require_once 'config.php'; require_login(); $page_title='Reports'; $me=current_user();
$type=$_GET['type']??'daily'; if(!in_array($type,['daily','weekly','monthly','custom'],true)) $type='daily';
$today=date('Y-m-d'); $from=$_GET['from']??$today; $to=$_GET['to']??$today;
if($type==='daily') $to=$from;
elseif($type==='weekly') { if(!date_valid($from)) $from=$today; $dt=new DateTime($from); $dt->modify('monday this week'); $from=$dt->format('Y-m-d'); $dt->modify('sunday this week'); $to=$dt->format('Y-m-d'); }
elseif($type==='monthly') { if(!date_valid($from)) $from=$today; $dt=new DateTime($from); $from=$dt->format('Y-m-01'); $to=$dt->format('Y-m-t'); }
if(!date_valid($from)||!date_valid($to)||$from>$to){$from=$today;$to=$today;}
$params=[$from,$to]; $where="e.engagement_date BETWEEN ? AND ?";
if($me['role']==='faculty'){ $where.=" AND e.faculty_id=?"; $params[]=$me['id']; }
if(!empty($_GET['faculty_id']) && $me['role']==='admin'){ $where.=" AND e.faculty_id=?"; $params[]=(int)$_GET['faculty_id']; }
if(!empty($_GET['department_id']) && $me['role']==='admin'){ $where.=" AND u.department_id=?"; $params[]=(int)$_GET['department_id']; }
$sql="SELECT e.engagement_date,e.period_name,e.status,e.remarks,u.full_name,d.department_name FROM engagement e JOIN users u ON u.id=e.faculty_id LEFT JOIN departments d ON d.id=u.department_id WHERE $where ORDER BY e.engagement_date DESC,u.full_name,e.period_name";
$stmt=db()->prepare($sql); $stmt->execute($params); $rows=$stmt->fetchAll();
$total=count($rows); $engaged=count(array_filter($rows,fn($r)=>$r['status']==='Engaged')); $not=$total-$engaged; $rate=$total?round($engaged*100/$total,1):0;
$faculty=db()->query("SELECT id,full_name FROM users WHERE role='faculty' ORDER BY full_name")->fetchAll();
$departments=db()->query("SELECT id,department_name FROM departments ORDER BY department_name")->fetchAll();
require 'header.php';
?>
<h1>Engagement reports</h1><section class="panel"><form method="get" class="report-filters">
<div><label>Report type</label><select name="type" id="reportType"><option value="daily" <?=$type==='daily'?'selected':''?>>Daily</option><option value="weekly" <?=$type==='weekly'?'selected':''?>>Weekly</option><option value="monthly" <?=$type==='monthly'?'selected':''?>>Monthly</option><option value="custom" <?=$type==='custom'?'selected':''?>>Custom date range</option></select></div>
<div><label><?= $type==='monthly'?'Choose month (any date in month)':'Start / selected date' ?></label><input type="date" name="from" value="<?=e($_GET['from']??$today)?>" required></div>
<?php if($me['role']==='admin'): ?><div><label>Faculty</label><select name="faculty_id"><option value="">All faculty</option><?php foreach($faculty as $f): ?><option value="<?=$f['id']?>" <?=($_GET['faculty_id']??'')==$f['id']?'selected':''?>><?=e($f['full_name'])?></option><?php endforeach; ?></select></div><div><label>Department</label><select name="department_id"><option value="">All departments</option><?php foreach($departments as $d): ?><option value="<?=$d['id']?>" <?=($_GET['department_id']??'')==$d['id']?'selected':''?>><?=e($d['department_name'])?></option><?php endforeach; ?></select></div><?php endif; ?>
<button class="btn primary">Generate report</button></form>
<?php if($type==='custom'): ?><p class="muted small">For a custom range, use the date below as the start date and add <code>to</code> as an end date in the URL, or use the date range inputs on the next revision. Current report uses the selected start date only.</p><?php endif; ?>
</section>
<div class="stats"><div class="stat"><span>Total periods</span><strong><?=$total?></strong></div><div class="stat"><span>Engaged</span><strong><?=$engaged?></strong></div><div class="stat"><span>Not engaged</span><strong><?=$not?></strong></div><div class="stat"><span>Engagement rate</span><strong><?=$rate?>%</strong></div></div>
<section class="panel"><div class="panel-head"><div><h2>Report details</h2><p class="muted"><?=e($from)?> to <?=e($to)?></p></div><div class="actions"><a class="btn outline" href="export.php?format=csv&amp;<?=e(http_build_query($_GET))?>">Export Excel (CSV)</a><button class="btn outline" type="button" onclick="window.print()">Print / Save PDF</button></div></div>
<div class="table-wrap"><table><thead><tr><th>Date</th><?php if($me['role']==='admin'): ?><th>Faculty</th><th>Department</th><?php endif; ?><th>Period</th><th>Status</th><th>Remarks</th></tr></thead><tbody>
<?php foreach($rows as $r): ?><tr><td><?=e($r['engagement_date'])?></td><?php if($me['role']==='admin'): ?><td><?=e($r['full_name'])?></td><td><?=e($r['department_name']??'—')?></td><?php endif; ?><td><?=e($r['period_name'])?></td><td><span class="pill <?=$r['status']==='Engaged'?'good':'bad'?>"><?=e($r['status'])?></span></td><td><?=e($r['remarks'])?></td></tr><?php endforeach; ?>
<?php if(!$rows): ?><tr><td colspan="6" class="empty">No records found for the selected filters.</td></tr><?php endif; ?></tbody></table></div></section>
<?php require 'footer.php'; ?>
