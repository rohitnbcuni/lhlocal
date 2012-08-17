	<!----| START: Header |---->
	<div class="wrapper header_container">
		<!----| START: Main Logo |---->
		<?PHP
			if(@$_SESSION['login_status'] != "client") {
				if(!isset($_SESSION['user_id'])) {
					echo '<a href="/login/">';
				} else {
					echo '<a href="/resourceplanner/?userid='.$_SESSION['user_id'] .'">';
				}
			} else {
				echo '<a href="/controltower/">';
			}
		?>
		<h1>Lighthouse</h1>
		</a>
		<!----| END: Main Logo |---->
	
		<!----| START: Navigation |---->
			<?PHP
			$menu_array1 = $menu_array;
				if($_controller != "login" && count($menu_array) > 0) {
					
					echo "<ul class=\"navigation\">";
					$i = 1;
					foreach ($menu_array as $conf_controller=>$menu_array_values)
					{
						//echo $conf_controller;
						if($menu_array_values['user_access'] == '1')
						{
							if($conf_controller == "resourceplanner" )
							{	
								$query = "/?userid=".$_SESSION['user_id'];
							} else {
								$query = "";
							}									
							if($conf_controller == 'admin'){
								continue;
							}else{
							if($conf_controller == $_controller) {
								$current_class = "current_tab";
								$openlink = "<a href=\"/" .$conf_controller .$query ."\"><span>";
								$closelink = "</span></a>";
							} else {
								$current_class = "";
								$openlink = "<a href=\"/" .$conf_controller .$query ."\">";
								$closelink = "</a>";
							}
							
							if($i == sizeof($menu_array)) {
								if(!empty($current_class)) {
									$current_class .= " current_tab_last";
								}
								else {
									$current_class .= " last";
								}
							}
							echo "<li class=\"$current_class\">$openlink"; 
							if($_SESSION['login_status'] == "client" && $conf_controller == "controltower" ) {
								echo "Account Info";
							} else {
								echo $menu_array_values['name'] ;
							}									
							echo "$closelink</li>\n\t\t\t";
							
						}							
						$i++;
					}
					}
					
				foreach ($menu_array1 as $conf_controller=>$menu_array_values)
					{
						if($conf_controller =='admin'){
							if($conf_controller == $_controller) {
								$current_class = "current_tab";
								$openlink = "<a href=\"/" .$conf_controller .$query ."\"><span>";
								$closelink = "</span></a>";
							} else {
								$current_class = "";
								$openlink = "<a href=\"/" .$conf_controller .$query ."\">";
								$closelink = "</a>";
							}
							
							if($i == sizeof($menu_array)) {
								if(!empty($current_class)) {
									$current_class .= " current_tab_last";
								}
								else {
									$current_class .= " last";
								}
							}
							echo "<li class=\"$current_class\">$openlink"; 
							echo $menu_array_values['name'] ;
								echo "$closelink</li>\n\t\t\t";
						}
						
					}
				
					echo "</ul>";
				}
			?>
		<!----| END: Navigation |---->
	</div>
	<!----| END: Header |---->