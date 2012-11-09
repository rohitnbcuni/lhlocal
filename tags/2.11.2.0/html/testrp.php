<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>NBCUXD: Lighthouse</title>
	<link href="_css/style.css" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" href="_css/ui.datepicker.css" />
	<!--[if IE]>
	<link rel="stylesheet" type="text/css" href="_css/style_ie.css" />
	<![endif]-->
	<script src="_js/jquery.js" type="text/javascript"></script>
	<script src="_js/jquery.bgiframe.js" type="text/javascript"></script>

	<script src="_js/jquery.dimensions.js" type="text/javascript"></script>
	<script src="_js/jquery.jhelpertip.js" type="text/javascript"></script>
	<script src="_js/ui.datepicker.js" type="text/javascript"></script>
	<!-- <script src="http://dev.jquery.com/view/tags/ui/latest/ui/ui.core.js"></script>
	<script src="http://dev.jquery.com/view/tags/ui/latest/ui/ui.sortable.js"></script> -->
	
	<script language="javascript">
		function show_hideDropdown(valUe) {
			var theId = "dropdown_menu_"+valUe;
			var curVal = document.getElementById(theId).style.display;
			var theArrowId = "arrow_"+valUe;
			
			if(curVal == "block") {
				document.getElementById(theId).style.display = "none";
				document.getElementById(theArrowId).className = "arrow_right";
			}
			if(curVal == "none") {
				document.getElementById(theId).style.display = "block";
				document.getElementById(theArrowId).className = "arrow_down";
			}
		}

		$(function() {
			$(".datePick input").datepicker({ 
				showOn: "both",
				buttonImage: "_images/date_picker_trigger.gif", 
				buttonImageOnly: true 
			});
			
			$("#projectCode").change(function(){
     			checkProjectStatus();
			})
			$("#projectName").change(function(){
     			checkProjectStatus();
			})		
			
		});


		function checkProjectStatus() {
			if (($("#projectCode").val() != "") && ($("#projectName").val() != "")) {
				$("#createProject").removeClass('inactive');
				$("#createProject").addClass('active');
			}
		}


	</script>

</head>

<body>

	
	
	<!----| START: Setting Bar |---->
	<div class="settings_bar">
		<div class="wrapper">
			<h2>NBCUXD: Experience User Design</h2>
			<ul class="settings">
				<li class="first">User Name</li>
				<li><a href="">My Profile</a></li>

				<li><a href="">Sign Out</a></li>
			</ul>
		</div>
	</div>
	<!----| END: Setting Bar |---->
	
	
	<!----| START: Header |---->
	<div class="wrapper header_container">
		<!----| START: Main Logo |---->

		<h1>Lighthouse</h1>
		<!----| END: Main Logo |---->
	
		<!----| START: Navigation |---->
		
		<!--
		<ul class="navigation">
			<li class="current_tab"><span>Dashboard</span></li>
			<li><a href="">Resource Planner</a></li>
			<li><a href="">Control Tower</a></li>
			<li><a href="">Work Orders</a></li>
			<li class="last"><a href="" class="last">Launch Calendar</a></li>
		</ul>

		<ul class="navigation">
			<li><a href="">Dashboard</a></li>
			<li class="current_tab"><span>Resource Planner</span></li>
			<li><a href="">Control Tower</a></li>
			<li><a href="">Work Orders</a></li>
			<li class="last"><a href="" class="last">Launch Calendar</a></li>
		</ul>
		-->

		<ul class="navigation">
			<li><a href="">Resource Planner</a></li>
			<li class="current_tab"><span>Control Tower</span></li>

			<li><a href="">Work Orders</a></li>
			<li class="last"><a href="" class="last">Launch Calendar</a></li>
		</ul>

		<!--
		<ul class="navigation">
			<li><a href="">Dashboard</a></li>
			<li><a href="">Resource Planner</a></li>
			<li><a href="">Control Tower</a></li>
			<li class="current_tab"><span>Work Orders</span></li>
			<li class="last"><a href="" class="last">Launch Calendar</a></li>
		</ul>

		<ul class="navigation">
			<li><a href="">Dashboard</a></li>
			<li><a href="">Resource Planner</a></li>
			<li><a href="">Control Tower</a></li>
			<li><a href="">Work Orders</a></li>
			<li class="current_tab current_tab_last"><span>Launch Calendar</span></li>
		</ul>
		-->
		
		<!----| END: Navigation |---->
	</div>
	<!----| END: Header |---->

	
	
	<!--==| START: Content |==-->
	<div class="wrapper">
		<div class="content_container">
	
<!--=========== START: COLUMNS ===========-->
		
			<div class="column_main_resource">
				
				<!--==| START: Bucket |==-->
				<div class="main_actions main_actions_resource">
					<button class="monitors"><span>Week loading: 56%</span></button>

					<button class="monitors"><span>quarter loading: 48%</span></button>
					<button class="monitors"><span>year loading: 94%</span></button>
					<button><span>run report</span></button>
				</div>
				<!--==| END: Bucket |==-->
			
				<!--==| START: Bucket |==-->
				<div class="title_med">

					<div class="left_actions">
						<label>Displaying</label>
						<select class="resource_types">
							<option>All Resource Types</option>
						</select>
						<label>for 8/04/08 - 8/08/08</label>
					</div>

					<div class="right_actions datePick">
						<label class="small">Jump to Date</label>
						<input type="text" value="--" id="basics"/>
					</div>
				</div>
				<!--==| END: Bucket |==-->
			
				<!--==| START: Bucket |==-->
				<div class="resources_controller">

					<ul class="alphalist">
						<li class="jumpto">Jump To</li>
						<li><a href="">a</a></li>
						<li><a href="">b</a></li>
						<li><a href="">c</a></li>
						<li><a href="">e</a></li>

						<li><a href="">f</a></li>
						<li><a href="">h</a></li>
						<li><a href="">i</a></li>
						<li><a href="">j</a></li>
						<li><a href="">k</a></li>
						<li><a href="">m</a></li>

						<li><a href="">n</a></li>
						<li><a href="">o</a></li>
						<li><a href="">p</a></li>
						<li><a href="">r</a></li>
						<li><a href="">s</a></li>
						<li><a href="">t</a></li>

						<li><a href="">u</a></li>
						<li><a href="">v</a></li>
						<li><a href="">w</a></li>
						<li><a href="">y</a></li>
					</ul>
					<ul class="days_container">
						<li class="arrows"><button class="arrows"></button></li>

						<li class="first_day">Mon<br/>08/04</li>
						<li>Tues<br/>08/05</li>
						<li>Wed<br/>08/06</li>
						<li>Thu<br/>08/07</li>
						<li class="last_day">Fri<br/>08/08</li>

						<li class="arrows"><button class="arrows arrows_right"></button></li>
					</ul>
				</div>
				<!--==| END: Bucket |==-->
			
				<!--==| START: Bucket |==-->
				<div class="schedules_container">
				
					<?PHP
						$_GET["startDate"] = "2008-12-1";
						$_GET["endDate"] = "2008-12-5";
						include('_ajaxphp/resource_planner_load2.php');
					?>
										
				</div>	
				<!--==| END: Bucket |==-->
				
			</div>

<!--=========== COLUMN BREAK ===========-->

			<div class="column_right_resource">
			
				<!--==| START: Bucket |==-->
				<div class="title_med"><h4>Allocation Type</h4></div>
				<div class="allocation_type">
					<ul>
					<li>De-select current selection</li>

					<li class="key_overhead">Overhead / Internal</li>
					<li class="key_outoffice">Out of Office</li>
					<li class="key_blank">Unassigned</li>
					<li class="key_convertbillable">Convert hours to Billable</li>
					</ul>
				</div>
				<!--==| END: Bucket |==-->

				
				<!--==| START: Bucket |==-->
				<div class="title_med">
					<h4>Hours</h4>
					<div class="right_actions right_actions_small">
						<input name="" type="radio" value="" /><label class="small">Scheduled</label><input name="" type="radio" value="" /><label class="small">Actual</label>
					</div>
				</div>

				<div class="hours">
					<ul>
					
											<li class="even"><div onClick="show_hideDropdown(0)" class="inside"><div class="arrow_right" id="arrow_0"></div> Bigpoint</div>
							<ul id="dropdown_menu_0" style="display: none;">
								<li><div class="inside">- <a href="#">CNBC0001 - Discussion</a></div></li>
								<li><div class="inside">- <a href="#">CNBC0001 - Discussion</a></div></li>

								<li><div class="inside">- <a href="#">CNBC0001 - Discussion</a></div></li>
								<li><div class="inside">- <a href="#">CNBC0001 - Discussion</a></div></li>
							</ul>
						</li>
											<li class="odd"><div onClick="show_hideDropdown(1)" class="inside"><div class="arrow_right" id="arrow_1"></div> Bigpoint</div>
							<ul id="dropdown_menu_1" style="display: none;">

								<li><div class="inside">- <a href="#">CNBC0001 - Discussion</a></div></li>
								<li><div class="inside">- <a href="#">CNBC0001 - Discussion</a></div></li>
								<li><div class="inside">- <a href="#">CNBC0001 - Discussion</a></div></li>
								<li><div class="inside">- <a href="#">CNBC0001 - Discussion</a></div></li>
							</ul>

						</li>
											<li class="even"><div onClick="show_hideDropdown(2)" class="inside"><div class="arrow_right" id="arrow_2"></div> Bigpoint</div>
							<ul id="dropdown_menu_2" style="display: none;">
								<li><div class="inside">- <a href="#">CNBC0001 - Discussion</a></div></li>
								<li><div class="inside">- <a href="#">CNBC0001 - Discussion</a></div></li>
								<li><div class="inside">- <a href="#">CNBC0001 - Discussion</a></div></li>

								<li><div class="inside">- <a href="#">CNBC0001 - Discussion</a></div></li>
							</ul>
						</li>
											<li class="odd"><div onClick="show_hideDropdown(3)" class="inside"><div class="arrow_right" id="arrow_3"></div> Bigpoint</div>
							<ul id="dropdown_menu_3" style="display: none;">
								<li><div class="inside">- <a href="#">CNBC0001 - Discussion</a></div></li>

								<li><div class="inside">- <a href="#">CNBC0001 - Discussion</a></div></li>
								<li><div class="inside">- <a href="#">CNBC0001 - Discussion</a></div></li>
								<li><div class="inside">- <a href="#">CNBC0001 - Discussion</a></div></li>
							</ul>
						</li>
											<li class="even"><div onClick="show_hideDropdown(4)" class="inside"><div class="arrow_right" id="arrow_4"></div> Bigpoint</div>

							<ul id="dropdown_menu_4" style="display: none;">
								<li><div class="inside">- <a href="#">CNBC0001 - Discussion</a></div></li>
								<li><div class="inside">- <a href="#">CNBC0001 - Discussion</a></div></li>
								<li><div class="inside">- <a href="#">CNBC0001 - Discussion</a></div></li>
								<li><div class="inside">- <a href="#">CNBC0001 - Discussion</a></div></li>

							</ul>
						</li>
											<li class="odd"><div onClick="show_hideDropdown(5)" class="inside"><div class="arrow_right" id="arrow_5"></div> Bigpoint</div>
							<ul id="dropdown_menu_5" style="display: none;">
								<li><div class="inside">- <a href="#">CNBC0001 - Discussion</a></div></li>
								<li><div class="inside">- <a href="#">CNBC0001 - Discussion</a></div></li>

								<li><div class="inside">- <a href="#">CNBC0001 - Discussion</a></div></li>
								<li><div class="inside">- <a href="#">CNBC0001 - Discussion</a></div></li>
							</ul>
						</li>
											<li class="even"><div onClick="show_hideDropdown(6)" class="inside"><div class="arrow_right" id="arrow_6"></div> Bigpoint</div>
							<ul id="dropdown_menu_6" style="display: none;">

								<li><div class="inside">- <a href="#">CNBC0001 - Discussion</a></div></li>
								<li><div class="inside">- <a href="#">CNBC0001 - Discussion</a></div></li>
								<li><div class="inside">- <a href="#">CNBC0001 - Discussion</a></div></li>
								<li><div class="inside">- <a href="#">CNBC0001 - Discussion</a></div></li>
							</ul>

						</li>
											<li class="odd"><div onClick="show_hideDropdown(7)" class="inside"><div class="arrow_right" id="arrow_7"></div> Bigpoint</div>
							<ul id="dropdown_menu_7" style="display: none;">
								<li><div class="inside">- <a href="#">CNBC0001 - Discussion</a></div></li>
								<li><div class="inside">- <a href="#">CNBC0001 - Discussion</a></div></li>
								<li><div class="inside">- <a href="#">CNBC0001 - Discussion</a></div></li>

								<li><div class="inside">- <a href="#">CNBC0001 - Discussion</a></div></li>
							</ul>
						</li>
											<li class="even"><div onClick="show_hideDropdown(8)" class="inside"><div class="arrow_right" id="arrow_8"></div> Bigpoint</div>
							<ul id="dropdown_menu_8" style="display: none;">
								<li><div class="inside">- <a href="#">CNBC0001 - Discussion</a></div></li>

								<li><div class="inside">- <a href="#">CNBC0001 - Discussion</a></div></li>
								<li><div class="inside">- <a href="#">CNBC0001 - Discussion</a></div></li>
								<li><div class="inside">- <a href="#">CNBC0001 - Discussion</a></div></li>
							</ul>
						</li>
											<li class="odd"><div onClick="show_hideDropdown(9)" class="inside"><div class="arrow_right" id="arrow_9"></div> Bigpoint</div>

							<ul id="dropdown_menu_9" style="display: none;">
								<li><div class="inside">- <a href="#">CNBC0001 - Discussion</a></div></li>
								<li><div class="inside">- <a href="#">CNBC0001 - Discussion</a></div></li>
								<li><div class="inside">- <a href="#">CNBC0001 - Discussion</a></div></li>
								<li><div class="inside">- <a href="#">CNBC0001 - Discussion</a></div></li>

							</ul>
						</li>
									</ul>
				</div>
				<!--==| END: Bucket |==-->
				
			</div>

<!--=========== END: COLUMNS ===========-->
		
		</div>
	</div>

	<!--==| END: Content |==-->

	<!--div id="popHours" class="popHours">
	<div class="popHeader"></div>
	<div class="popMain">
		<table>
		<tr>
			<td colspan=2>
				<label>Projects:</label>
				<select style="width: 290px;">
					<option value=1>Name / Number</option>
				</select>
			</td>
		</tr>
		<tr>
			<td width="80">
				<label>Hours:</label>
				<select style="width: 57px;">
					<option value=1></option>
				</select>
			</td>
			<td>
				<label>Mile Stone:</label>
				<select style="width: 207px;">
					<option value=1>Select current milestone</option>
				</select>
			</td>
		</tr>
		<tr>
			<td colspan=2>
				<label>Notes:</label>
				<textarea style="width: 290px; height: 48px"></textarea>
			</td>
		</tr>
		<tr>
			<td colspan=2 align="right">
				<button id="saveBtn"><img src="images/save_button.gif"></button>
			</td>
		</tr>
		</table>
	</div>
	<div class="popFooter"></div>
	</div-->
	
	
	<div id="blur" class="blur jHelperTipClose"></div>
	<!----| START: Footer |---->
	<div class="footer_container">
		<!--<ul class="footer_nav">
		<li></li>
		</ul>
		
		<div class="legal"></div>-->
	</div>
	<!----| END: Footer |---->

	
	
</body>
</html>
