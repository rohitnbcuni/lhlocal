<?php

session_start();
include('../_inc/config.inc');
include("sessionHandler.php");
//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);	
global $mysql;
//$mysql = new mysqli('localhost', 'root','' , 'lhnew_db', '3306');

$project_query = "Select com.name AS 'Company', 
CONCAT(pjt.project_code, ' : ', pjt.project_name) AS 'Project',
CONCAT(dmu.first_name, ' ', dmu.last_name) AS 'Project Manager',
CONCAT(elu.first_name, ' ', elu.last_name) AS 'Engagement Lead', 
(case pjt.project_status
when '1' then 'Incoming'
when '2' then 'In Pitch'
when '3' then 'Active'
when '4' then 'On Hold'
when '5' then 'Killed'
when '6' then 'Complete'
when '7' then 'Internal'
end) AS 'Status', 
(case pjt.program
when '1' then 'Publishing'
when '2' then 'Video'
when '3' then 'Identity'
when '4' then 'Mobile'
when '5' then 'ATV'
when '6' then 'Content Intelligence'
when '7' then 'Social Media'
when '8' then 'Revenue Enablers'
when '9' then 'Site Solutions'
when '10' then 'Operations'
end) AS 'Program', 
bud.total_budget AS 'Total Allocation', 
bud.quarter1_budget AS 'Q1 Allocation', 
bud.quarter2_budget AS 'Q2 Allocation', 
bud.quarter3_budget AS 'Q3 Allocation', 
bud.quarter4_budget AS 'Q4 Allocation',
IFNULL(pjt.budget_code, '') AS 'Charge Code'
FROM projects pjt
LEFT JOIN companies com ON pjt.company=com.id
LEFT JOIN project_budget bud ON bud.project_id=pjt.id
LEFT JOIN (SELECT * from project_phase_approvals where non_phase='client') ppa ON ppa.project_id=pjt.id
LEFT JOIN (SELECT * FROM project_roles where resource_type_id='3') dm ON dm.project_id=pjt.id
LEFT JOIN (SELECT * FROM project_roles where resource_type_id='2') el ON el.project_id=pjt.id
LEFT JOIN users dmu ON dm.user_id=dmu.id
LEFT JOIN users elu ON el.user_id=elu.id
LEFT JOIN 
(Select tab1.projectid, sum(tab1.Total*tab2.rate) AS amount from 
(Select tab3.projectid, tab3.userid, sum(tab3.Total) AS Total from
(Select rb.projectid, rb.userid, count(1) AS Total  from projects pj, resource_blocks rb where pj.id=rb.projectid and pj.active='1' and pj.deleted='0' and pj.archived='0'  and rb.status='4' and rb.daypart <> 9 group by pj.id, rb.userid
UNION
Select rb.projectid, rb.userid, rb.hours AS Total  from projects pj, resource_blocks rb where pj.id=rb.projectid and pj.active='1' and pj.deleted='0' and pj.archived='0' and rb.status='4' and rb.daypart = 9) tab3
group by tab3.projectid, tab3.userid) tab1, (select upr.project_id , upr.phase_subphase_id, upr.user_id, ppf.rate from user_project_role upr, project_phase_finance ppf where upr.project_id=ppf.project_id and upr.phase_subphase_id=ppf.phase and upr.flag='phase'
UNION
select upr.project_id , upr.phase_subphase_id, upr.user_id, pspf.rate from user_project_role upr, project_sub_phase_finance pspf where upr.project_id=pspf.project_id and upr.phase_subphase_id=pspf.sub_phase and upr.flag='subphase'
UNION
Select rb.projectid, ppf.phase, rb.userid, ppf.rate from resource_blocks rb, users us, project_phase_finance ppf WHERE rb.userid = us.id AND rb.projectid = ppf.project_id AND  us.role=ppf.phase and rb.userid NOT IN (SELECT DISTINCT user_id from user_project_role upr where upr.project_id = rb.projectid) group by rb.projectid, rb.userid) tab2 WHERE tab1.projectid = tab2.project_id and tab1.userid = tab2.user_id
group by tab1.projectid) budget ON pjt.id=budget.projectid
WHERE pjt.company <> '0' and pjt.archived='0' and pjt.deleted='0' and pjt.project_status IN (3,6)
GROUP BY com.name, pjt.project_code, pjt.bc_id";
//echo "qry".$project_query;
$project_result = $mysql->sqlordie($project_query) or die(mysql_error());
//$project_result->num_rows;

  $header = "Company\t Project\t Project Manager\t Engagement Lead\t Status\t Program\t Total Allocation\t Q1 Allocation\t Q2 Allocation\t Q3 Allocation\t Q4 Allocation\t Charge Code\n";
  $excel_body = '';

while($wo=$project_result->fetch_assoc()){ 
  
          $excel_body .=  $wo['Company'] . "\t " .
          $wo['Project'] . "\t " .
          $wo['Project Manager'] . "\t " .
          $wo['Engagement Lead'] . "\t " .
          $wo['Status'] . "\t " .
          $wo['Program'] . "\t " .
          $wo['Total Allocation'] . "\t " .
          $wo['Q1 Allocation'] . "\t " .
          $wo['Q2 Allocation'] . "\t " .
          $wo['Q3 Allocation'] . "\t " .
          $wo['Q4 Allocation'] . "\t".
          $wo['Charge Code'] . "\n";
  }

	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Type: application/force-download");
	header("Content-Type: application/octet-stream");
	header("Content-Type: application/download");
	header("Content-Disposition: attachment;filename=allocation_report.xls"); 
	header("Content-Transfer-Encoding: binary ");
	echo $header;
	echo $excel_body;
?>
