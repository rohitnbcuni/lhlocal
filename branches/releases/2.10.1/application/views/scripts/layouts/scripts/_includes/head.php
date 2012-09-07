<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?PHP	$randNum = "20120907"; ?>
<title><?echo DEV_TEAM_NAME?>: Lighthouse</title>
	<link rel="stylesheet" href="/_css/style.css?<?PHP echo $randNum; ?>" type="text/css" />
	<link rel="stylesheet" href="/_css/ui.datepicker.css" type="text/css" />
	<link rel="stylesheet" href="/_css/jqueryMultiSelect.css" type="text/css" />
	<link rel="icon" href="<?php echo BASE_URL ;?>/_images/Favicon.ico" type="image/vnd.microsoft.icon" />
	<link rel="shortcut icon" type=image/x-icon href="<?php echo BASE_URL;?>/_images/Favicon.ico" />
	<link rel=icon type=image/ico href="<?php echo BASE_URL;?>/_images/Favicon.ico" />
<?PHP
if(isset($_SESSION['login_status']) && $_SESSION['login_status'] == "client"){
?>
	<link rel="stylesheet" href="/_css/style_client.css?<?PHP echo $randNum; ?>" type="text/css" />

<?PHP
}
	$agent = $_SERVER['HTTP_USER_AGENT'];
	if(eregi("safari", $agent)) {
		echo '<style>
			.schedules_container {margin: 6px 4px 6px 0px;}
		</style>';
	}
?>

<!--[if IE]>
<link rel="stylesheet" type="text/css" href="/_css/style_ie.css?<?PHP echo $randNum; ?>" />
<![endif]-->
<script type="text/javascript" src="/_fckeditor/fckeditor.js"></script>
<script src="/_js/jquery.pack.js" type="text/javascript"></script>
<script src="/_js/lh_global.js" type="text/javascript"></script>
 <script src="/_js/s_code.js" type="text/javascript"></script>
<script language="JavaScript" type="text/javascript"><!--

	//s.pageName=document.title; 
	s.pageType="";  // used for 404 Error message page only
	s.channel="Home";
		s.prop1="";
		s.prop2="";
		s.prop3="";
		s.prop4="";
		s.prop5="";
		 
		/************* DO NOT ALTER ANYTHING BELOW THIS LINE ! **************/
		var s_code=s.t();if(s_code)document.write(s_code)//--></script>
		<script type="text/javascript"><!--
		if(navigator.appVersion.indexOf('MSIE')>=0)document.write(unescape('%3C')+'!-'+'-')
		//--></script><!--/DO NOT REMOVE/-->
		<!-- End SiteCatalyst code version: H.22.1 -->
	<script type="text/javascript" charset="utf-8">
		if($.browser.msie && $.browser.version<7 && !window.location.href.match(/ie6\.php/)){
			window.location.href='/ie6.php';
		}
	</script>
	<?PHP
	if(isset($_SESSION['login_status']) && $_SESSION['login_status'] == "client"){
	?>
		<script src="/_js/lh_global_client.js" type="text/javascript"></script>
	
	<?PHP
	}
//	 Zend_Debug::dump("head");
		switch($_controller) {
		
			//Resource Planner
			case 'resourceplanner': {

					echo "<script src=\"/_js/ui.core.js\" type=\"text/javascript\"></script>\n"
					 ."\t<script src=\"/_js/ui.selectable.js\" type=\"text/javascript\"></script>\n"
					 ."\t<script src=\"/_js/ui.datepicker.js\" type=\"text/javascript\"></script>\n";
				
					echo "\t<script src=\"/_js/resource_planner.js?" . $randNum . "\" type=\"text/javascript\"></script>\n";


				break;
			}
			//Control Tower
			case 'controltower': {
				echo "<script src=\"/_js/ui/jquery.ui.all.js\" type=\"text/javascript\"></script>\n";

				switch(@$_action) {
					case 'create': {
						echo "\t<script src=\"/_js/controltower.create.js?" . $randNum . "\" type=\"text/javascript\"></script>\n"
							."\t<script src=\"/_js/ui.datepicker.js\" type=\"text/javascript\"></script>\n"
							//."\t<script src=\"/_js/jquery.dimensions.js\" type=\"text/javascript\"></script>\n"
							."\t<script src=\"/_js/jqueryMultiSelect.js\" type=\"text/javascript\"></script>\n";
						break;
					}
					case 'edit': {
						echo "\t<script src=\"/_js/controltower.create.js?" . $randNum . "\" type=\"text/javascript\"></script>\n"
							."\t<script src=\"/_js/ui.datepicker.js\" type=\"text/javascript\"></script>\n"
							//."\t<script src=\"/_js/jquery.dimensions.js\" type=\"text/javascript\"></script>\n"
							."\t<script src=\"/_js/jqueryMultiSelect.js\" type=\"text/javascript\"></script>\n"
							."\t<script src=\"/_js/jquery.formatCurrency-1.4.0.js\" type=\"text/javascript\"></script>\n";
						break;
					}
                                        //LH#22669
					case 'calendarview': {
						echo "\t<script src=\"/_js/ui.datepicker.js\" type=\"text/javascript\"></script>\n"
							."\t<script src=\"/_js/ct_calendar_view.js\" type=\"text/javascript\"></script>\n"
							."\t<script src=\"/_js/jqueryMultiSelect.js\" type=\"text/javascript\"></script>\n";
							
						break;
					}
					//End
					default: {
						echo "\t<META HTTP-EQUIV=\"CACHE-CONTROL\" CONTENT=\"NO-CACHE\">\n";
						echo "\t<META HTTP-EQUIV=\"Expires\" CONTENT=\"0\">\n";
						echo "\t<META HTTP-EQUIV=\"Pragma\" CONTENT=\"no-cache\">\n";
						echo "\t<script src=\"/_js/ct_quickfilter.js?" . $randNum . "\" type=\"text/javascript\"></script>\n";
						break;
					}
				}

				break;
			}
			//Wor Orders
			case 'workorders': {

				echo '	<script language="javascript">
							var RT_OUTAGE = 1;
							var RT_PROBLEM = 2;
							var RT_CHANGE = 3;
							var RT_QA = 4;
							var SEVERITY1 = 5;
							var SEVERITY2 = 6;
							var SEVERITY3 = 7;
							var SUPPORT_TEAM_ITOC_ID = "'.SUPPORT_TEAM_ITOC_ID.'";
							var SUPPORT_TEAM_ITOC_NAME = "'.SUPPORT_TEAM_ITOC_NAME.'";
							var SUPPORT_TEAM_ID = "'.SUPPORT_TEAM_ID.'";
							var SUPPORT_TEAM_NAME = "'.SUPPORT_TEAM_NAME.'";
							var MAINTENANCE_TEAM_ID = "'.MAINTENANCE_TEAM_ID.'";
							var MAINTENANCE_TEAM_NAME = "'.MAINTENANCE_TEAM_NAME.'";
							var WO_CREATE_OUTAGE = \''.WO_CREATE_OUTAGE.'\';
              var WO_CREATE_CHANGE = \''.WO_CREATE_CHANGE.'\';
              var WO_CREATE_PROBLEM = \''.WO_CREATE_PROBLEM.'\';
						</script>';	
				switch(@$_action) {
					case 'edit': {}
					case 'create': {

						echo	"<script src=\"/_js/jquery-1.7.2.min.js\" type=\"text/javascript\"></script>\n"
							."\t<script src=\"/_js/ui/jquery.ui.all.js\" type=\"text/javascript\"></script>\n"
							."\t<script src=\"/_js/workorders.create.js?" . $randNum . "\" type=\"text/javascript\"></script>\n"
							."\t<script src=\"/_js/ajaxfileupload.js\" type=\"text/javascript\"></script>\n"
							."\t<script src=\"/_js/jquery-ui-1.8.19.custom.min.js\" type=\"text/javascript\"></script>"
							."\t<script src=\"/_js/jquery.ui.widget.js\" type=\"text/javascript\"></script>\n"
							."\t<script src=\"/_js/jquery.notify.js\" type=\"text/javascript\"></script>\n"
							. "\t<link href=\"/_css/jquery-ui.css?" . $randNum ."\" rel=\"stylesheet\" type=\"text/css\"/>";
						echo  "<link href=\"/_css/ui.notify.css?" . $randNum ."\" rel=\"stylesheet\" type=\"text/css\"/>";	

							
						break;
					}//Ticket #7927 Add new js file for calender view :wo_calender_view.js
					case 'calendarview':{
						echo "<script src=\"/_js/wo_calender_view.js?" . $randNum . "\" type=\"text/javascript\"></script>\n";
						echo "<script src=\"/_js/workorders.filter.js?" . $randNum . "\" type=\"text/javascript\"></script>\n";
						echo "\t<script src=\"/_js/jqueryMultiSelect.js\" type=\"text/javascript\"></script>\n";
						echo "\t<script src=\"/_js/ui.datepicker.js\" type=\"text/javascript\"></script>\n";
					    break;
					}//End
                                         //Ticket #14012
					case 'search':{
						echo '';
						break;						
					}//end 
					default: {
					echo	"<script src=\"/_js/jquery-1.7.2.min.js\" type=\"text/javascript\"></script>\n";
					echo "<script src=\"/_js/workorders.filter.js?" . $randNum . "\" type=\"text/javascript\"></script>\n";
					echo "\t<script src=\"/_js/jqueryMultiSelect.js\" type=\"text/javascript\"></script>\n"
						."\t<script src=\"/_js/jquery-ui-1.8.19.custom.min.js\" type=\"text/javascript\"></script>"
						. "\t<link href=\"/_css/jquery-ui.css?" . $randNum ."\" rel=\"stylesheet\" type=\"text/css\"/>";
					//echo "\t<script src=\"/_js/ui.datepicker.js\" type=\"text/javascript\"></script>\n";
					}
				}

				break;
			}
			//QA Work Orders
			case "quality": {	
				switch(@$_action) {
					case 'edit': {}
					case 'create': {

						echo "<script src=\"/_js/ui/jquery.ui.all.js\" type=\"text/javascript\"></script>\n"
							."\t<script src=\"/_js/quality.create.js?" . $randNum . "\" type=\"text/javascript\"></script>\n"
							."\t<script src=\"/_js/ajaxfileupload.js\" type=\"text/javascript\"></script>\n"
							."\t<script src=\"/_js/ui.datepicker.js\" type=\"text/javascript\"></script>\n";
						break;
					}
					default: {
						echo "<script src=\"/_js/quality.filter.js?" . $randNum . "\" type=\"text/javascript\"></script>\n";
					}
				}

				break;
			}

		case "admin": {	
		switch(@$_action) {
					case 'fetchUser': {
						echo "<script src=\"/_js/jquery-1.7.2.min.js\" type=\"text/javascript\"></script>\n"
						."\t<script src=\"/_js/ui/jquery-ui-1.8.13.custom.min.js\" type=\"text/javascript\"></script>\n";
						echo "<link href=\"/_css/jquery.multiselect_new.css?" . $randNum ."\" rel=\"stylesheet\" type=\"text/css\"/>\n"
						."\t<link href=\"/_css/jquery.multiselect.filter.css?" . $randNum ."\" rel=\"stylesheet\" type=\"text/css\"/>\n"
						."\t<link href=\"/_css/jquery.ui.multiselect.css?" . $randNum ."\" rel=\"stylesheet\" type=\"text/css\"/>\n"
						."\t<script src=\"/_js/ui/jquery.multiselect_new.js\" type=\"text/javascript\"></script>\n"
						//."\t<script src=\"/_js/ui/jquery.multiselect.js\" type=\"text/javascript\"></script>\n"
						."\t<script src=\"/_js/ui/jquery.multiselect.filter.js\" type=\"text/javascript\"></script>\n"
						."\t<script src=\"/_js/admin.js?" . $randNum . "\" type=\"text/javascript\"></script>\n";
						
						break;
						
					}				
		
				default: {
					echo "<script src=\"/_js/ui/jquery.ui.all.js\" type=\"text/javascript\"></script>\n"
						."\t<script src=\"/_js/admin.js?" . $randNum . "\" type=\"text/javascript\"></script>\n"
						."\t<script src=\"/_js/ui.datepicker.js\" type=\"text/javascript\"></script>\n";
					break;
				}
				break;
			}
			break;
		}
			//Launch Calendar
			case "launchcalendar": {
				//echo "<script src=\"/_js/dom-drag.js\" type=\"text/javascript\"></script>\n";
				echo "<script src=\"/_js/launchcalendar.view.js\" type=\"text/javascript\"></script>\n";
				break;
			}
			case "events": {
		
			switch(@$_action) {
					case "create":{
					echo "<link rel=\"stylesheet\" href=\"/_css/events.css?". $randNum. "\" type=\"text/css\" />";
					echo "<script src=\"/_js/tooltip.js\" type=\"text/javascript\"></script>\n"
					."\t<script src=\"/_js/ui.datepicker.js\" type=\"text/javascript\"></script>\n";
					break;
					}
					case "listview":{
					echo "<link rel=\"stylesheet\" href=\"/_css/events.css?". $randNum. "\" type=\"text/css\" />";
					echo "<script src=\"/_js/tooltip.js\" type=\"text/javascript\"></script>\n"
					."\t<script src=\"/_js/ui.datepicker.js\" type=\"text/javascript\"></script>\n";
					break;
					}
					case "calendarview":{
					echo "<script src=\"/_js/jquery.min.js\"></script>"; 
					echo "<link rel=\"stylesheet\" href=\"/_css/events.css?". $randNum. "\" type=\"text/css\" />";
					echo "<script src=\"/_js/tooltip.js\" type=\"text/javascript\"></script>\n"
					."\t<script src=\"/_js/ui.datepicker.js\" type=\"text/javascript\"></script>\n";
					break;
					}
					case "detail":{
					echo "<link rel=\"stylesheet\" href=\"/_css/events.css?". $randNum. "\" type=\"text/css\" />";
					echo "<script src=\"/_js/tooltip.js\" type=\"text/javascript\"></script>\n"
					."\t<script src=\"/_js/ui.datepicker.js\" type=\"text/javascript\"></script>\n";
					break;
					}
					
				} 
			break;
			}
		}
		if($_controller == "login") {
			echo "<style>"
			."body { background-image: url('/_images/background_login.jpg')}"
			."</style>";
			echo "<script src=\"/_js/jquery.validate.js\" type=\"text/javascript\"></script>\n";
			echo "<script src=\"/_js/lh_login.js\" type=\"text/javascript\"></script>\n";
		}	
?>
<?php
$ua = $_SERVER['HTTP_USER_AGENT'];
$checker = array(
  'iphone'=>preg_match('/iPhone|iPod|iPad/', $ua),
  'blackberry'=>preg_match('/BlackBerry/', $ua),
  'android'=>preg_match('/Android/', $ua),
);

if ($checker['iphone']){

echo "<style>"
."body {-webkit-background-size:1590px}"
."</style>";

}

?>
	<script language="javascript">
		var sBasePath = document.location.href;
	</script>

</head>

<body>

