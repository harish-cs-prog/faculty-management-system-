<?php
require_once 'config.php'; require_login(); $me=current_user();
$from=$_GET['from']??date('Y-m-d'); $to=$_GET['to']??$from; $type=$_GET['type']??'daily';
if(!date_valid($from)) $from=date('Y-m-d');
if($type==='weekly'){ $d=new DateTime($from); $d->modify('monday this week'); $from=$d->format('Y-m-d'); $d->modify('sunday this week'); $to=$d->format('Y-m-d'); }
elseif($type==='monthly'){ $d=new DateTime($from); $from=$d->format('Y-m-01'); $to=$d->format('Y-m-t'); }
elseif($type==='daily') $to=$from;
if(!date_valid($to)||$from>$to) $to=$from;
$params=[$from,$to]; $where="e.engagement_date BETWEEN ? AND ?";
if($me['role']==='faculty'){ $where.=" AND e.faculty_id=?"; $params[]=$me['id']; }
if($me['role']==='admin'&&!empty($_GET['faculty_id'])){ $where.=" AND e.faculty_id=?"; $params[]=(int)$_GET['faculty_id']; }
if($me['role']==='admin'&&!empty($_GET['department_id'])){ $where.=" AND u.department_id=?"; $params[]=(int)$_GET['department_id']; }
$s=db()->prepare("SELECT e.engagement_date,u.full_name,d.department_name,e.period_name,e.status,e.remarks FROM engagement e JOIN users u ON u.id=e.faculty_id LEFT JOIN departments d ON d.id=u.department_id WHERE $where ORDER BY e.engagement_date,u.full_name,e.period_name"); $s->execute($params); $rows=$s->fetchAll();
header('Content-Type: text/csv; charset=utf-8'); header('Content-Disposition: attachment; filename="faculty_engagement_report_'.$from.'_to_'.$to.'.csv"');
$out=fopen('php://output','w'); fwrite($out,"\xEF\xBB\xBF"); fputcsv($out,['Date','Faculty','Department','Period','Status','Remarks']);
foreach($rows as $r) fputcsv($out,$r); fclose($out); exit;
