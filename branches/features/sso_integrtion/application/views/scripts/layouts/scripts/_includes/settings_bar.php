	<!----| START: Setting Bar |---->
	<div class="settings_bar">
		<div class="wrapper">
			<h2>Powered By</h2>
			<ul class="settings">
			<?PHP
			include("../simplesamlphp/lib/_autoload.php");
			$auth = new SimpleSAML_Auth_Simple('nbcu-sp');
			//$auth->logout();
			$b = BASE_URL."/login/?signout=true";
		    $url = $auth->getLogoutURL($b);
				//@$menu_array[5]['url']
				if(@$menu_url[0] != "login") {
					echo '<li class="risk"><button style="display:none" onclick="showUserRisks(\'' . $_SESSION['user_id'] . '\');" id="back_button"><span class="my_risk">you have ' . $my_risk_count . '</span></button></li>';
				 echo '<li class="first">' .@$_SESSION['lh_username'] .'</li>
                                        <!--<li><a href="">My Profile</a></li>-->
                                        <li><a href="'.$url.'">Sign Out</a></li>';		
		
		$pageURL = BASE_URL; 

		?><li style="border-left:none;padding: 0 4px;"><div id="search_top">
		<div><form action="<?php echo $pageURL;?>/search" method="post" name="search_box_form" id="search_box_form" >
		<input name="search_text" id="search_text" type="text" class="search_bg"  tabindex="1" placeholder="  search" class="textbox"  autocomplete="off" onkeyup="ajax_showOptions(this,'q',event)" maxlength="100">
		<input name="bt_search" type="button" class="bt_search" id="bt_search"  >

		<div class="bt_advSearch" id="bt_advSearch"  >
		<img src="<?php echo $pageURL;?>/_images/adv_option.png"  style="cursor: pointer;" title="Advance Search" />
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
</div></li><?php } 	?>
			</ul>
		</div>
	</div>
	
	<!----| END: Setting Bar |---->

	<div class="advance_search_container">
		<div class="close_advance_search" title="close">X</div>
			<div class="advance_search_content panel_search">
				
				<form action="<?php echo $pageURL;?>/search/index/advancesearch" method="post" name="advance_search_form" id="advance_search_form"  autocomplete="off">
					
					<center><div style="margin-top:15px;" id="error_msg"></div></center>
					<h4 id="searchbykeyword" class="keyword">Search By Keyword:</h4>
					<div style="width:600px;">
						<div  class="search-group" style="float:left;">
							<div class="keyword option">
							<label for="all">All</label>
							<input id="all" class="keyword" name="allOptions" type="text" maxlength="50">
							</div>
							<div class="keyword option">
							<label for="atLeastOne">Or</label>
							<input id="atLeastOne" class="keyword" name="atLeastOne" type="text" maxlength="50">
							</div>
							<div class="keyword option">
							<label for="without">Not</label>
							<input id="without" class="keyword" name="without" type="text" maxlength="50">
									   <input  type="hidden" name="search_par[]" id="search_par" value="All"  />
							</div>
							
						</div>
						<div  class="search-group " style="float:right;">
							<div class="keyword option">
								<label for="startdate">Start Date</label>
								<input type="text" id="search_startdate" maxlength="50" name="search_startdate"  readonly="readonly" style="margin-left: -1px;" />
							</div>
							
							<div class="keyword option">
								<label for="enddate">End Date</label>
								<input type="text" id="search_enddate" name="search_enddate" maxlength="50" readonly="readonly" style="margin-left: -1px;" />
							</div>
						
	  
							<div class="keyword option">
							<fieldset id="search-fieldset">
								<legend>Search By Fields:</legend>
								<div style="width:203px;height:7px;">
										<div style="float:left;">
										<input type="checkbox" id="search_fields" name="search_fields[]"  value="docid" checked="checked" />:
										Id
										</div>
										<div style="float:right;">
										<input type="checkbox" id="search_fields" name="search_fields[]"  value="title" checked="checked" />:
										Title
										</div>
								</div>
								<br/>
								<div style="width:234px;">
										<div style="float:left;">
											<input type="checkbox" id="search_fields" name="search_fields[]"  value="description" checked="checked" />:
											Description
										</div>
										<div style="float:right;">
											<input type="checkbox" id="search_fields" name="search_fields[]"  value="commentTextList" checked="checked" />:
											Comments
										</div>
								</div>
							</fieldset>
							</div>
						</div>
					</div>
					<div style="hight:50px;"></div>
					<div style="padding-left:235px;"><button onClick="return advance_search(); return false;" ><span>SEARCH</span></button></div>
				</form>
				
			</div>
	</div>