<?PHP

	include('LaunchCalendar.inc');
	
	class Launchcalendar_IndexController extends LighthouseController  { 
		public function indexAction() {
			$project_html = LcDisplay::getProjectsHTML();
			$stages_html = LcDisplay::getStagesHTML();
			$timeline_weeks = LcDisplay::getWeeksHTML();
			
			echo '<!--==| START: Filter |==-->
						<div class="launch_calendar_filter">
							<button onClick="lcFilter(true);"><span>all projects</span></button>
							<div class="lc_filters">
								<label>Client:</label>'
								.LcDisplay::getCompanyHTML()
							.'</div>
							<div class="lc_filters">
								<label>Engagement Leads:</label>'
								.LcDisplay::getUsersHTML()
							.'</div>
						</div>
						<!--==| END: Filter |==-->

						<!--==| START: Actions |==-->
						<div class="launch_calendar_actions">
							<button onClick="weeksView(3);"><span>3 Weeks</span></button>
							<button onClick="weeksView(6);"><span>6 Weeks</span></button>
							<button onClick="weeksView(9);"><span>9 Weeks</span></button>
						</div>
						<!--==| END: Actions |==-->

						<div class="launch_calendar_container">
							<!--===================| START: Title Area |===================-->
							<div class="lc_title_container">
								<div class="title_xlrg"><strong>Projects</strong></div>

								<div class="lc_week_controller">
									<div id="project_header" class="lc_weeks_scroller" onClick="getlefts();">'
										.$timeline_weeks
									.'</div>
									<button class="arrows" onClick="scrollDivsLeft(); return false;"></button>
									<button class="arrows arrows_right" onClick="scrollDivsRight(); return false;"></button>
								</div>
							</div>

						<!--===================| END: Title Area |===================-->

						<!--===================| START: Main |===================-->
						<div class="lc_calendar_container_1"><div class="lc_calendar_container_inner_1"><div class="lc_calendar_container_backdrop_1">
							<div class="lc_calendar_container_scroller">
							
								<!--==| START: Projects |==-->'
								.$project_html
								.'<!--==| END: Projects |==-->
							
								<!--==| START: Timlines |==-->'
								.$stages_html
								.'<!--==| END: Timlines |==-->
							
							</div>
						</div></div></div>

						<div class="lc_calendar_container_2"><div class="lc_calendar_container_inner_2"></div></div>
						<!--===================| END: Main |===================-->

						</div>';
		}
	}
?>