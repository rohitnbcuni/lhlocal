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
					echo '<li class="risk"><button style="display:none" onclick="showUserRisks(\'' . $_SESSION['user_id'] . '\');" id="back_button"><span class="my_risk">you have ' . $my_risk_count . '</span></button></li>';
				 echo '<li class="first">' .@$_SESSION['lh_username'] .'</li>
                                        <!--<li><a href="">My Profile</a></li>-->
                                        <li><a href="/login/?signout=true">Sign Out</a></li>';		
 $pageURL = 'http';
 if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
 $pageURL .= "://";
$pageURL .= $_SERVER["SERVER_NAME"];

		?><li><div id="search_top">
	<div><form action="<?php echo $pageURL;?>/search" method="post" name="search_box_form"><input name="search_text" id="search_text" type="text" class="search_bg"><input name="" type="button" class="bt_search" onClick="document.search_box_form.submit();"><div class="bt_advSearch"> <a href="javascript:toggleDiv();"><img src="/_images/images/adv_option.png" /></a> 
        <div id="popup_top" > 
          <div class="search_popupTop"> <a class="bt_close"  href="javascript:hide_searchpopup();"><img src="/_images/images/bt_close.png" /></a> 
            <input  type="checkbox" name="search_par[]" id="search_par" value="All"  checked="checked"/>
            All<br />
            <input type="checkbox"  name="search_par[]"  id="search_par1" value="Defect" />
            Defects<br />
            <input  type="checkbox" name="search_par[]" id="search_par2"  value="WorkO" />
            Work Orders</div>
          <div class="search_popupBottom"></div>
        </div>
      </div>
		</form>
	</div>
</div></li><?php 
					/*echo '<li class="first">' .@$_SESSION['lh_username'] .'</li>
					<!--<li><a href="">My Profile</a></li>-->
					<li><a href="/login/?signout=true">Sign Out</a></li>';*/
				}
			?>
			</ul>
		</div>
	</div>
	<!--<div class="user_risk_container">
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
	</div>-->
	<!----| END: Setting Bar |---->

