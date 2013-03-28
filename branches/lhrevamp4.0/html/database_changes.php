<?php

include("_inc/config.inc");
//include("_ajaxphp/sessionHandler.php");
 
global $mysql;

$projects_column = 'ALTER TABLE `projects` ADD `project_charter` VARCHAR( 100 ) NULL AFTER `program`,ADD `project_scope` VARCHAR( 100 ) NULL AFTER `project_charter`';

//$project_budget_code = 'ALTER TABLE `projects` DROP `budget_code`';

$project_status = 'ALTER TABLE `project_status` ADD `note` VARCHAR( 100 ) NULL';
$project_phases = 'ALTER TABLE `project_phases` ADD `updated_by` INT( 10 ) NULL AFTER `deleted`,ADD `updated_on` TIMESTAMP NULL AFTER `updated_by` ,ADD `note` VARCHAR( 100 ) NULL AFTER `updated_on`';

//$project_budget = 'ALTER TABLE `project_budget` ADD `budget_code` VARCHAR( 150 ) NULL DEFAULT NULL ,ADD `note` VARCHAR( 100 ) NULL DEFAULT  NULL ,ADD `delete_flag` INT( 11 ) NOT NULL';

$proj_tab = "INSERT INTO `lh4_setup`.`lnk_project_brief_section_types` (`id`, `name`, `sort_order`, `active`, `deleted`) VALUES (NULL, 'Overview', '0', '1', '0')";

$project_budget = 'ALTER TABLE `project_budget` ADD `updated_by` INT( 11 ) NOT NULL ,ADD `updated_on` TIMESTAMP NOT NULL ,ADD `budget_code` VARCHAR( 150 ) NULL DEFAULT NULL ,ADD `note` VARCHAR( 100 ) NULL DEFAULT NULL ,ADD `delete_flag` INT( 5 ) NOT NULL';

$res = $mysql->sqlordie($projects_column);
//$res = $mysql->sqlordie($project_budget_code);
$res = $mysql->sqlordie($project_status);
$res = $mysql->sqlordie($project_phases);
$res = $mysql->sqlordie($project_budget);
$res = $mysql->sqlordie($proj_tab);

echo 'Data base changes done!';


?>