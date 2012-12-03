<?php
ini_set('display_errors', 0);
define('DEV_TEAM_NAME', 'NBCU O&amp;TS');
//ini_set('error_reporting', E_ALL);
	$menu_array = array();
	$menu_array[0]['name'] = "Dashboard";
	$menu_array[0]['url'] = "";
	$menu_array[1]['name'] = "Resource Planner";
	$menu_array[1]['url'] = "resourceplanner";
	$menu_array[2]['name'] = "Control Tower";
	$menu_array[2]['url'] = "controltower";
	$menu_array[3]['name'] = "Work Orders";
	$menu_array[3]['url'] = "workorders";
	$menu_array[4]['name'] = "Launch Calendar";
	$menu_array[4]['url'] = "launchcalendar";
	//$menu_array[5]['name'] = "Login";
	//$menu_array[5]['url'] = "login";
	//substring it to get rid of beginning /
	$menu_url = explode("/", substr($_SERVER['REQUEST_URI'], 1));
	$menu_url=array();
	$menu_url[0]='login';
	if($menu_url[0] == "login") {
		$login_class = " content_container_login";
	} else {
		$login_class = "";
	}
	
	include "../application/views/scripts/layouts/scripts/_includes/head.php";
	include "../application/views/scripts/layouts/scripts/_includes/settings_bar.php";
	include "../application/views/scripts/layouts/scripts/_includes/navigation.php" ;
	
	
?>
<style>

.navigation{display:none;}
</style>
	<!--==| START: Content |==-->
	<div class="wrapper">
		<div class="content_container<?PHP echo $login_class; ?>">
			<div class="login_box_container">
								<div class="login_box">
									<div class="login_box_inner">
										<p>Lighthouse is optimized for modern browsers including; Firefox 2+, Internet Explorer 7+ and Safari. It is not compatible with your current browser.</p>

										   <p>To use Lighthouse, you can download the latest version of
										   Firefox here:</p>
										   <a href="http://www.mozilla.com" target="_blank">http://www.mozilla.com</a>
									</div>
								</div>
							</div>

		
		</div>
	</div>
	<!--==| END: Content |==-->	
	
	<div id="blur" class="blur jHelperTipClose"></div>
<?PHP
	include "../application/views/scripts/layouts/scripts/_includes/footer.php"
?>
