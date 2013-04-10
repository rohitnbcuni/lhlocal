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
			<h2>Powered By</h2>
			<ul class="settings">
			<?PHP
				//@$menu_array[5]['url']
				if(@$menu_url[0] != "login") {
					echo '<li class="risk"><button style="display:none" onclick="showUserRisks(\'' . $_SESSION['user_id'] . '\');" id="back_button"><span class="my_risk">you have ' . $my_risk_count . '</span></button></li>';
				 echo '<li class="first">' .@$_SESSION['lh_username'] .'</li>
                                        <!--<li><a href="">My Profile</a></li>-->
                                        <li><a href="/login/?signout=true">Sign Out</a></li>';		
		
		$pageURL = BASE_URL; 

		?><li style="border-left:none;padding: 0 4px;"><div id="search_top">
		<div><form action="<?php echo $pageURL;?>/search" method="post" name="search_box_form" id="search_box_form" >
		<input name="search_text" id="search_text" type="text" class="search_bg"  tabindex="1" placeholder="  search" class="textbox"  autocomplete="off" onkeyup="ajax_showOptions(this,'q',event)" maxlength="100">
		<input name="bt_search" type="button" class="bt_search" id="bt_search"  >

		<div class="bt_advSearch" id="bt_advSearch"  > <a href="javascript:void(null);" >
		<img src="/_images/images/adv_option.png" alt="Advance Search" title="Advance Search"/></a>
		<div style="display:none;">
        <div id="popup_top"  > 
          <div class="search_popupTop"> <a class="bt_close"  href="javascript:hide_searchpopup();"><img src="/_images/images/bt_close.png" /></a> 
           <input  type="radio" name="search_par[]" id="search_par" value="All"  checked="checked"/>
            All<br />
            <input type="radio"  name="search_par[]"  id="search_par1" value="Defect" />
            Defects<br />
            <input  type="radio" name="search_par[]" id="search_par2"  value="WorkO" />
            Work Orders</div>
          <div class="search_popupBottom"></div>
        </div>
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

	<div class="advance_search_container">
		<div class="close_advance_search" title="close">X</div>
			<div class="advance_search_content panel_search">
				
				<form action="<?php echo $pageURL;?>/search/index/advancesearch" method="post" name="advance_search_form" id="advance_search_form" >
					
					<center><div style="margin-top:15px;" id="error_msg"></div></center>
					<h4 id="searchbykeyword" class="keyword">Search By Keyword:</h4>
					<div aria-labelledby="searchbykeyword" role="group">
					<div class="keyword option">
					<label for="all">All</label>
					<input id="all" class="keyword" name="allOptions" type="input" maxlength="50">
					</div>
					<div class="keyword option">
					<label for="atLeastOne">Or</label>
					<input id="atLeastOne" class="keyword" name="atLeastOne" type="input" maxlength="50">
					</div>
					<div class="keyword option">
					<label for="without">Not</label>
					<input id="without" class="keyword" name="without" type="input" maxlength="50">
					           <input  type="hidden" name="search_par[]" id="search_par" value="All"  />
					</div>
					</div>
					<div style="padding-left:50px;"><button onClick="advance_search(); return false;" ><span>SEARCH</span></button></div>
				</form>
				
			</div>
	</div>