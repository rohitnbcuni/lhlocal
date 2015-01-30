

	defined('APPLICATION_PATH')
    or define('APPLICATION_PATH', '../../application');

	defined('APPLICATION_ENVIRONMENT')
    or define('APPLICATION_ENVIRONMENT', 'development');


	define('ZENDLIB', APPLICATION_PATH.'/../ZendFramework/library');
	define('ZENDINCUBATOR', ZENDLIB.'/../laboratory');
	
	//define('APPLIB',"/home/nbclh/www/lighthouse/");
	define('APPPATH', APPLICATION_PATH);
	define('APPLIB', APPPATH.'/library');
	define('WEBPATH', APPPATH.'/..');
	define('FCKPATH', WEBPATH.'/_fckeditor');
	define('FPDF_FONTPATH', APPPATH.'/font/');
	if(isset($_SERVER['HTTP_HOST']))
	{
		define('BASE_URL', 'http://' .$_SERVER['HTTP_HOST']);
		define('BASE_URL_secure', 'https://' .$_SERVER['HTTP_HOST']);
	}else{
		define('BASE_URL', 'http://lighthouse.nbcuxd.com');
		define('BASE_URL_secure', 'https://lighthouse.nbcuxd.com');
	}

    define('BASE_URL_FILE_PATH',BASE_URL."/_ajaxphp/download.php?path=");
	// current year
	define('current_year','2012');
	define('COMMENT_REPLY_TO_EMAIL','lighthousedev.comments@nbcuni.com');
/*

	//App environment
	define('APPLICATION_ENVIRONMENT', 'development');
	//Include paths. Only add the $includePaths ZENDINCUBATOR if 
	//Zend incubator code is supected to be used
	define('ZENDLIB', '/var/www/lighthouse_v2/ZendFramework/library/');
	define('ZENDINCUBATOR', '/var/www/lighthouse_v2/ZendFramework/laboratory/');
	
	//define('APPLIB',"/home/nbclh/www/lighthouse/");
	define('APPPATH', '/var/www/lighthouse_v2/application/');
	define('APPLIB', '/var/www/lighthouse_v2/application/library/');
	define('WEBPATH', '/var/www/lighthouse_v2/html/');
	define('FCKPATH', '/var/www/lighthouse_v2/html/_fckeditor');
	define('FPDF_FONTPATH', '/var/www/lighthouse_v2/application/font/');
	define('BASE_URL', 'http://' .$_SERVER['HTTP_HOST']);
	define('BASE_URL_secure', 'https://' .$_SERVER['HTTP_HOST']);
	
*/
	
		//Basecamp vars
        define ('BASECAMP_HOST', 'https://nbcudps.basecamphq.com');
        define ('BASECAMP_PORT', 443);
        define ('BASECAMP_USER', 'resourceplanner@nbcuxd.com');
        define ('BASECAMP_PASS', 'r3s0urc3');
        define ('BASECAMP_HOST_URL', 'nbcudps.basecamphq.com');
        define ('BASECAMP_COOKIE', 'C:/nbcudps.basecamphq.com.cookie');
        define ('WO_EMAIL_FROM', 'lighthouse@nbcuots.com');

        /**************************************************/
        //Old Paths
        define( 'FS_BASE_PATH', '/var/www/tools/');
        //define('BASECAMP_HOST', 'https://nbcuxd.grouphub.com');
        define('BASECAMP_USERNAME', 'shobhitsingh.bhadauria@nbcuni.com');
        define('BASECAMP_PASSWORD', 'y114dff3');
        //define( 'FS_BASE_PATH', '/var/www/phpframework/');
        define('BASECAMP_RALLY_USERNAME', 'PlatformUser');
        define('BASECAMP_RALLY_PASSWORD', 'password');

        define( 'LOCAL_TEMPLATE_DIR', '/var/www/tools/templates/');

        define('DB_TYPE', 'mysql');

	define('DB_SERVER', 'localhost');
	define('DB_USERNAME', 'root');
	define('DB_PASSWORD', '');
	define('DB_DATABASE', 'lighthouse_new');
	define('DB_PORT', '3306');

	define('LIGHTHOUSE_VERSION', 'localhost-2.11.23-branch');

	//Rally
	define('RALLY_USERNAME', 'scott.baierwalter@nbc.com');
	define('RALLY_PASSWORD', 'Lighth0use123');
	///////////////RALLY PROJECT URL   //////////
	
	//define('XML_POST_URL', 'https://rally1.rallydev.com/slm/webservice/1.40/defect/10378972627');
	
	define('RALLY_DEFECT_USERNAME', "scott.baierwalter@nbc.com");
	define('RALLY_DEFECT_PASSWORD', "Lighth0use123");
	define("RALLY_WEB_SERVICE_URL","https://rally1.rallydev.com/slm/webservice/1.40");
	define("RALLY_LH_USER_ID",'10361771291');
	define('RALLY_PROJECT_URL', RALLY_WEB_SERVICE_URL."/subscription?fetch=Workspaces,Name,ObjectID,Projects");
	
	/////////////END RALLY//////////
	
	define('DEV_TEAM_NAME', 'NBCU O&amp;TS');

	//define('BASECAMP_HOST', 'https://nbcuxd.grouphub.com');
	//define('BASECAMP_USERNAME', 'zeb.holt@nbcuni.com');
	//define('BASECAMP_PASSWORD', 'zebzeb007');

	$GLOBALS['DB_DEBUG'] = 0;
	
	// workorder maintainance user
	/*
	 * <option selected="" value="97">Maintenance, UXD</option>
<option value="1369">DPS Hosting DB team</option>1369
<option value="1206">DPS Hosting Web Team</option>1206

<option value="1369">MPAT Hosting DB team</option>
<option value="1206">MPAT Hosting Web Team</option>
<option value="2433">OTS Release Team</option>
	 */
	
	
	
	define('SUPPORT_TEAM_ITOC_ID', '1932'); //1328
	define('SUPPORT_TEAM_ITOC_NAME', 'DPS Support Team');
	define('SUPPORT_TEAM_ID', '1932'); //1328
	define('SUPPORT_TEAM_NAME', 'DPS Support Team');
	define('MAINTENANCE_TEAM_ID', '1932'); //97
	define('MAINTENANCE_TEAM_NAME', 'Maintenance, UXD');
//	define('WO_CREATE_OUTAGE', '[{"id":"106","name":"DPS Support Team","selected":" selected "}]');
//	define('WO_CREATE_CHANGE', '[{"id":"1932","name":"Maintenance, UXD","selected":" selected "},{"id":"1932","name":"DPS Hosting DB team","selected":""},{"id":"1932","name":"DPS Hosting Web Team","selected":""}]');
//	define('WO_CREATE_PROBLEM', '[{"id":"1932","name":"DPS Support Team","selected":" selected "},{"id":"1932","name":"DPS Hosting DB team","selected":""},{"id":"1932","name":"DPS Hosting Web Team","selected":""}]');
    define('WO_CREATE_OUTAGE', '[{"id":"1932","name":"O&TS Support Team","selected":" selected "}]');
	define('WO_CREATE_CHANGE', '[{"id":"1932","name":"Maintenance, UXD","selected":" selected "},{"id":"1932","name":"MPAT Hosting DB team","selected":""},{"id":"1932","name":"MPAT Hosting Web Team","selected":""},{"id":"1932","name":"OTS Release Team","selected":""},{"id":"4216","name":"Splunk Support","selected":""}]');
	define('WO_CREATE_PROBLEM', '[{"id":"1932","name":"O&TS Support Team","selected":" selected "},{"id":"1932","name":"MPAT Hosting DB team","selected":""},{"id":"1932","name":"MPAT Hosting Web Team","selected":""}]');

	// Control Tower 
	define('UNASSIGNED_PHASE', '8');
	define('UNASSIGNED_PHASE_RATE', '100');
	define('DEFAULT_INTERNAL_GROUP', '1');
	/*******************************************************
	*Global query strings
	*******************************************************/
	//define('',"");
	define('QRY_PROJECT_SECTIONS_ASC',"SELECT * FROM `lnk_project_brief_section_types` ORDER BY `sort_order` ASC");
	define('QRY_COMPANIES_ASC',"SELECT * FROM `companies` ORDER BY `name` ASC");
	define('QRY_USERS_ASC',"SELECT * FROM `users` WHERE `active`='1' AND `deleted`='0' ORDER BY `first_name`,`last_name` ASC");
        define('QRY_DPS_USERS_ASC',"SELECT * FROM `users` WHERE `company` =2 AND `active` = '1' AND `deleted` = '0' ORDER BY `first_name`,`last_name` ASC");
	define('QRY_USERS_LAST_ASC',"SELECT * FROM `users` WHERE `active`='1' AND `deleted`='0' ORDER BY `last_name` ASC");
	define('QRY_PROJECTS_ASC',"SELECT * FROM `projects` WHERE `archived` = '0' AND `active` = '1' AND `deleted` = '0' ORDER BY `project_name` ASC");
	define('QRY_PROJECTS_ARCHIVE_ASC',"SELECT * FROM `projects` WHERE `active` = '1' AND `deleted` = '0' ORDER BY `project_name` ASC");
	define('QRY_ROLES_SO_ASC', "SELECT * FROM `resource_types` WHERE `active`='1' AND `deleted`='0' ORDER BY `sort_order` ASC, `editable` ASC");
	define('QRY_TIMELINE_SO_ASC', "SELECT * FROM `lnk_project_phase_types` WHERE `active`='1' AND `deleted`='0' ORDER BY `sort_order` ASC");
	define('QRY_WO_PRIORITY_ASC', "SELECT * FROM `lnk_workorder_priority_types` WHERE `active`='1'AND `deleted`='0' ORDER BY `name` ASC");
	define('QRY_WO_STATUS_ASC', "SELECT * FROM `lnk_workorder_status_types` WHERE `active`='1' AND `deleted`='0' ORDER BY `name` ASC");

	if(isset($_COOKIE["lh_user"]) && AJAX_CALL != '0'){
		session_start();
		if(!isset($_SESSION["loggedin"])){
			$login = $_COOKIE["lh_user"];
			$login = base64_decode($login);
			$login_user_array = unserialize($login);
			foreach($login_user_array as $key => $value){
				$_SESSION[$key] = $value;
			}
		}
	}

	$USER_ACCESS = array (
		'RESOURCE_PLANNER' => '1',
		'CONTROL_TOWER' => '1',
		'WORKORDERS' => '1',
		'QUALITY' => '1',
		'ADMIN' => '1',
		'LAUNCHCALENDAR' => '0',
		'UNASSIGNED1' => '0',
		'UNASSIGNED2' => '0'
	);

	$USER_ACCESS_MENU['RESOURCE_PLANNER'] = array (
		'name' => 'Resource Planner',
		'url' => 'resourceplanner'
	);

	$USER_ACCESS_MENU['CONTROL_TOWER'] = array (
		'name' => 'Control Tower',
		'url' => 'controltower'
	);

	$USER_ACCESS_MENU['WORKORDERS'] = array (
		'name' => 'Work Orders',
		'url' => 'workorders'
	);

	$USER_ACCESS_MENU['QUALITY'] = array (
		'name' => 'Quality',
		'url' => 'quality'
	);

	$USER_ACCESS_MENU['ADMIN'] = array (
		'name' => 'Admin',
		'url' => 'admin'
	);

	$USER_ACCESS_MENU['LAUNCHCALENDAR'] = array (
		'name' => 'Launch Calendar',
		'url' => 'launchcalendar'
	);

	$USER_ACCESS = array (
		'RESOURCE_PLANNER' => '1',
		'CONTROL_TOWER' => '1',
		'WORKORDERS' => '1',
		'QUALITY' => '1',
		'ADMIN' => '1',
		'LAUNCHCALENDAR' => '0',
		'EVENTS' => '1',
		'SEARCH' => '1',
	    
	);

	$USER_ACCESS_MENU['RESOURCE_PLANNER'] = array (
		'name' => 'Resource Planner',
		'url' => 'resourceplanner'
	);

	$USER_ACCESS_MENU['CONTROL_TOWER'] = array (
		'name' => 'Control Tower',
		'url' => 'controltower'
	);

	$USER_ACCESS_MENU['WORKORDERS'] = array (
		'name' => 'Work Orders',
		'url' => 'workorders'
	);

	$USER_ACCESS_MENU['QUALITY'] = array (
		'name' => 'Quality',
		'url' => 'quality'
	);

	$USER_ACCESS_MENU['ADMIN'] = array (
		'name' => 'Admin',
		'url' => 'admin'
	);

	$USER_ACCESS_MENU['LAUNCHCALENDAR'] = array (
		'name' => 'Launch Calendar',
		'url' => 'launchcalendar'
	);
	$USER_ACCESS_MENU['EVENTS'] = array (
		'name' => 'Event Planner',
		'url' => 'events'
	);
	$USER_ACCESS_MENU['SEARCH'] = array (
		'name' => 'Search',
		'url' => 'search'
	);

	define("MAX_UPLOAD_FILE_SIZE",10485760);

	define("ALLOWED_FILE_EXTENSION",serialize(array('.jpg','.jpeg','.png','.gif','.tiff','.bmp',
	'.html','.txt','.xml','.xls','.xlsx','.pdf','.doc','.docx','.zip','.tar','.flv','.mp4',
	'.JPG','.JPEG','.PNG','.TIFF','.BMP','.HTML','.TXT','.XML','.XLS','.XLSX','.PDF','.DOC','.DOCX')));
	
	

	include_once("mysqli_Class.php");
	
	$mysql = new MySQLiDB(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	
	define("SOLR_URL_STRING","http://ec2-75-101-162-191.compute-1.amazonaws.com:8080/solr/lighthouse_active/select?q=");
	
	define("GOOGLE_ANALYTICAL_CODE","<script>
		  </script>");
	function p($arr){
		print "<pre>";
		print_r($arr);
		
	}
	
	define('REQUESTED_BY_COMPANIES','1932');
	define("ADMIN_TYPE_USER",serialize(array(2,136,141)));
	
	define("COMPANY_LABEL","NBCUniversal Business");
	
	define("BASECAMP_MAPPING","	");
    
    //Google Checklist Account Details
    define("GOOGLE_ACCOUNT","shobhitsingh1@gmail.com");
    define("GOOGLE_PASSWORD",base64_decode("dmlyYWFqMTZERUM="));
    define("GOOGLE_SPREADSHEETKEY","1vugzynFReaCFOYvMVJ55-GGNqV1V2njvD5zn3wNcgx8");
   //CHECKLIST CONSANT
   define("CHECKLIST_ASSIGNED_ID","2433");
   define("CHECKLIST_DEPLOYEMENT_ID","180");
    
	
?>
