<?php

include("_inc/config.inc");
include("sessionHandler.php");
 
global $mysql;

$projects_column = 'ALTER TABLE `projects` ADD `project_charter` VARCHAR( 100 ) NULL AFTER `program`,ADD `project_scope` VARCHAR( 100 ) NULL AFTER `project_charter`';

//$project_budget_code = 'ALTER TABLE `projects` DROP `budget_code`';

$project_status = 'ALTER TABLE `project_status` ADD `note` VARCHAR( 100 ) NULL';
$project_phases = 'ALTER TABLE `project_phases` ADD `updated_by` INT( 10 ) NULL AFTER `deleted`,ADD `updated_on` TIMESTAMP NULL AFTER `updated_by` ,ADD `note` VARCHAR( 100 ) NULL AFTER `updated_on`';

$project_budget = 'ALTER TABLE `project_budget` ADD `budget_code` VARCHAR( 150 ) NULL DEFAULT NULL ,ADD `note` VARCHAR( 100 ) NULL DEFAULT  NULL ,ADD `delete_flag` INT( 11 ) NOT NULL';

$res = $mysql->sqlordie($projects_column);
//$res = $mysql->sqlordie($project_budget_code);
$res = $mysql->sqlordie($project_status);
$res = $mysql->sqlordie($project_phases);
$res = $mysql->sqlordie($project_budget);


?>