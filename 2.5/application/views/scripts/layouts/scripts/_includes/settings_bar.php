<?PHP
		defined('APPLICATION_PATH')
		or define('APPLICATION_PATH', '../application');
		include_once (APPLICATION_PATH.'/../html/_inc/config.inc');
		$mysql_details = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
		include_once (APPLICATION_PATH.'/../html/_ajaxphp/get_data.php');
		$my_risk_count = get_user_risks(@$_SESSION['user_id'], $mysql_details);
		$my_risk_count .= ($my_risk_count == '1') ? ' risk':' risks';

?>
	<!----| START: Setting Bar |---->
	<div class="settings_bar">
		<div class="wrapper">
			<h2><?=DEV_TEAM_NAME?>: Experience User Design</h2>
			<ul class="settings">
			<?PHP
				//@$menu_array[5]['url']
				if(@$menu_url[0] != "login") {
					echo '<li class="risk"><button style="display: block;" onclick="showUserRisks(\'' . $_SESSION['user_id'] . '\');" id="back_button"><span class="my_risk">you have ' . $my_risk_count . '</span></button></li>';
					echo '<li class="first">' .@$_SESSION['lh_username'] .'</li>
					<!--<li><a href="">My Profile</a></li>-->
					<li><a href="/login/?signout=true">Sign Out</a></li>';
				}
			?>
			</ul>
		</div>
	</div>
	<div class="user_risk_container">
		<div class="close_risk">X</div>
		<div class="user_risk_content">
			<ul class="risk_results" id="user_risk_list">

			</ul>
		</div>
	</div>
	<script>
	$(document).ready(function(){
		$('.close_risk').click(function(){
			$('.user_risk_container').css({display:'none'});
		});
	});
	</script>
	<div class="message_risk_create">
		<p class="risk_msg">
		</p>
		<div style="clear: both;"></div>
		<div class="duplicate_buttons">
			<button onClick="$('.message_risk_create').css({display:'none'}); return false;"><span>OK</span></button>
			<div style="clear: both;"></div>
		</div>
	</div>
	<!----| END: Setting Bar |---->

