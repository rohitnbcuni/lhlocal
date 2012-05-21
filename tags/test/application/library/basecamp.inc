<?php
//define('HTTP_PROXY','64.210.197.20:80');
define('BC_URL',BASECAMP_HOST);
define('BC_USER','resourceplanner@nbcuxd.com');
define('BC_PASSWORD',"r3s0urc3");
define('BC_DB_HOST','localhost');
define('BC_DB_USER','nbclh');
define('BC_DB_PASSWORD','nbclh634');
define('BC_DB_NAME','tools');
define('THIS_COMPANY','NBC UXD');
define('RE_CAPTCH_PRIVATE_KEY','6Lf5B80SAAAAADF6LPCfaBtOYWBDFEwp-iumSgU8');
//define('BC_DB_PORT');
/*
/contacts/companies
/project/list
/contacts/people/707029
*/
class Basecamp{
	/*-----------------------Properties---------------------*/
	private $config;
	private $_session;
	
	var $mysql; //From Old php_lib

	/*-------------------------Methods----------------------*/

	/**
	 * Instantiate the class and setup connection parameters.
	 *
	 * @param $BasecampUrl
	 *   Your Basecamp Url. Should begin with http or https
	 * @param $username
	 *   Your Basecamp username.
	 * @param $password
	 *   Your Basecamp password.
	 */
	//public function __construct($BasecampUrl,$username,$password){
	public function __construct(){
		//Set the Zend config ini file in basecamp class
		$this->config = new Zend_Config_Ini(
				    APPPATH . '/config/app.ini', 
				    APPLICATION_ENVIRONMENT
				);
		$this->_session = new Zend_Session_Namespace('Zend_BC_Auth');
		$this->_session->setExpirationSeconds(365 * 24 * 60 * 60);
	}
	function basecamp()
	{
		if(defined('BC_DB_PORT') && BC_DB_PORT!='')
		{
			$this->mysql=new mysqli(BC_DB_HOST,BC_DB_USER,BC_DB_PASSWORD,BC_DB_NAME,BC_DB_PORT);
		}
		else
		{
			$this->mysql=new mysqli(BC_DB_HOST,BC_DB_USER,BC_DB_PASSWORD,BC_DB_NAME);
		}
		if (mysqli_connect_errno()) {
			printf("Connect failed: %s\n", mysqli_connect_error());
			exit();
		}
	}
	public function bcLogin() {
		//Login using blanck unfound url - optimizes basecamp connection
		$this->config = new Zend_Config_Ini(
			APPPATH . '/config/app.ini', 
			APPLICATION_ENVIRONMENT
		);
		$is_in_db = false;
		$user_session = array();
		
		if(isset($_COOKIE["lh_user"])) {
			$this->get_session("lh_user");
			
			return true;
		}else if(isset($_COOKIE["lighthouse_id"])) {
				$mysql = new mysqli($this->config->database->params->host,
				$this->config->database->params->username,
				$this->config->database->params->password,
				$this->config->database->params->dbname,
				$this->config->database->params->dbport);
			
			$get_user = "SELECT * FROM `users` WHERE `id`='" . $mysql->real_escape_string($_COOKIE["lighthouse_id"]) ."' AND `active` = '1'  AND `deleted` = '0' LIMIT 1";
			$get_user_res = $mysql->query($get_user);
			$get_user_row = $get_user_res->fetch_assoc();
			
			if($get_user_res->num_rows > 0) {
				if($get_user_row['delete'] != 1) {     
					$is_in_db = true;
				}
			}else{
				return false;
			}
			
			$this->_session->lh_username = $get_user_row['email'];
			$_SESSION['lh_username'] = $get_user_row['email'];
			$this->_session->lh_password = base64_decode($_COOKIE["lighthouse_xp"]);
			$_SESSION['lh_password'] = base64_decode($_COOKIE["lighthouse_xp"]);
			$this->_session->user_id = $get_user_row['id'];
			$_SESSION['user_id'] = $get_user_row['id'];
			$this->_session->first = $get_user_row['first_name'];
			$_SESSION['first'] = $get_user_row['first_name'];
			$this->_session->last = $get_user_row['last_name'];
			$_SESSION['last'] = $get_user_row['last_name'];
			$this->_session->login_status = $get_user_row['login_status'];
			$_SESSION['login_status'] = $get_user_row['login_status'];
			$this->_session->role = $get_user_row['role'];
			$_SESSION['role'] = $get_user_row['role'];
			$this->_session->resource = $get_user_row['resource']; 
			$_SESSION['resource'] = $get_user_row['resource'];
			$this->_session->company = $get_user_row['company'];   
			$_SESSION['company'] = $get_user_row['company'];
			$this->_session->user_access_bits = $get_user_row['user_access'];
			$_SESSION['user_access_bits'] = $get_user_row['user_access'];

			$user_session['lh_username'] = $get_user_row['email'];
			$user_session['lh_password'] = base64_decode($_COOKIE["lighthouse_xp"]);
			$user_session['user_id'] = $get_user_row['id'];
			$user_session['first'] = $get_user_row['first_name'];
			$user_session['last'] = $get_user_row['last_name'];
			$user_session['login_status'] = $get_user_row['login_status'];
			$user_session['role'] = $get_user_row['role'];
			$user_session['resource'] = $get_user_row['resource'];
			$user_session['company'] = $get_user_row['company'];
			$user_session['user_access_bits'] = $get_user_row['user_access'];

			if($get_user_row['login_status'] != "admin"){
				if($get_user_row['company'] == "2" || $get_user_row['company'] == "136" || $get_user_row['company'] == "141") {
					$login_status = "employee";
				} else {
					$login_status = "client";
				}
				$_SESSION['login_status'] = $login_status;
				$user_session['login_status'] = $login_status;
			}
			$this->_session->loggedin = true;
			$_SESSION['loggedin'] = true;
			$user_session['loggedin'] = true;

			$this->set_session($user_session, "lh_user");
			return true;
		} else {
			if(isset($_POST['lh_username']) && (!empty($_POST['lh_password']))) {
				//$mysql = new mysqli('localhost', 'generic', 'generic', 'nbc_lighthouse');
				$mysql = new mysqli($this->config->database->params->host,
				$this->config->database->params->username,
				$this->config->database->params->password,
				$this->config->database->params->dbname,
				$this->config->database->params->dbport);
				
				$get_user = "SELECT * FROM `users` WHERE `user_name`='" .$mysql->real_escape_string($_POST['lh_username']) ."' AND `active` = '1'  AND `deleted` = '0' LIMIT 1";
				$result = @$mysql->query($get_user);
				$row = @$result->fetch_assoc();
				
				if($result->num_rows > 0) {
					if($row['delete'] != 1) {
						$is_in_db = true;
					}
				}else{
					return false;
				}
				
				$this->_session->lh_username = $row['user_name'];
				$_SESSION['lh_username'] = $row['user_name'];
				$this->_session->lh_password = $_POST['lh_password'];
				$_SESSION['lh_password'] = $_POST['lh_password'];
				$this->_session->user_id = $row['id'];
				$_SESSION['user_id'] = $row['id'];
				$this->_session->first = $row['first_name'];
				$_SESSION['first'] = $row['first_name'];
				$this->_session->last = $row['last_name'];
				$_SESSION['last'] = $row['last_name'];
				$this->_session->login_status = $row['login_status'];
				$_SESSION['login_status'] = $row['login_status'];
				$this->_session->role = $row['role'];
				$_SESSION['role'] = $row['role'];
				$this->_session->resource = $row['resource'];
				$_SESSION['resource'] = $row['resource'];
				$this->_session->company = $row['company'];
				$_SESSION['company'] = $row['company'];
				$this->_session->user_access_bits = $row['user_access'];
				$_SESSION['user_access_bits'] = $row['user_access'];

				$user_session['lh_username'] = $row['user_name'];
				$user_session['lh_password'] = $_POST['lh_password'];
				$user_session['user_id'] = $row['id'];
				$user_session['first'] = $row['first_name'];
				$user_session['last'] = $row['last_name'];
				$user_session['login_status'] = $row['login_status'];
				$user_session['role'] = $row['role'];
				$user_session['resource'] = $row['resource'];
				$user_session['company'] = $row['company'];
				$user_session['user_access_bits'] = $row['user_access'];

				if($row['login_status'] != "admin"){
					if($row['company'] == "2" || $row['company'] == "136" || $row['company'] == "141") {
						$login_status = "employee";
					} else {
						$login_status = "client";
					}
					$_SESSION['login_status'] = $login_status;
					$user_session['login_status'] = $login_status;
				}
				//Add code for captch
				$atm = @base64_decode($_COOKIE['atm']);
				if($atm > 3){
			/*		require_once 'securimage/securimage.php';
     			 	$securimage = new Securimage();
     			 	$captcha = @$_POST['lh_captcha'];
     			 	//p($_SESSION);
     			 	if($securimage->check($captcha) == false){
     			 		$this->_session->loggedin = false;
						$_SESSION['loggedin'] = false;
						$user_session['loggedin'] = false;
						$_SESSION['loggedin'] = false;
						$_SESSION['captcha_error'] = 'Incorrect security code entered<br />';
     			 		return false;
     			 		
     			 	}else{
     			 		$_SESSION['captcha_error'] = NULL;
     			 		unset($_SESSION['captcha_error']);
     			 		//session_destroy($_SESSION['captcha_error']);
     			 	}
				}*/
				  $privatekey = RE_CAPTCH_PRIVATE_KEY;
					  $resp = recaptcha_check_answer ($privatekey,
					                                $_SERVER["REMOTE_ADDR"],
					                                $_POST["recaptcha_challenge_field"],
					                                $_POST["recaptcha_response_field"]);
					
					if(!$resp->is_valid){
     			 		$this->_session->loggedin = false;
						$_SESSION['loggedin'] = false;
						$user_session['loggedin'] = false;
						$_SESSION['loggedin'] = false;
						$_SESSION['captcha_error'] = 'Incorrect security code entered<br />';
     			 		return false;
     			 		
     			 	}else{
     			 		$_SESSION['captcha_error'] = NULL;
     			 		unset($_SESSION['captcha_error']);
     			 		//session_destroy($_SESSION['captcha_error']);
     			 	}
				}
				//End Captch code
				//session_cache_expire(480); // in minutes  - 8 hours
				
				
			}
			
			if($this->bcXML('/contacts/person/', $user_session) && $is_in_db) {
				$this->_session->loggedin = true;
				$_SESSION['loggedin'] = true;
				$user_session['loggedin'] = true;
				$this->set_session($user_session, "lh_user");
				if(@$_POST['remember'] == "on") {
					setcookie("lighthouse_id", $row['id'], time()+220752000, '/');
					setcookie("lighthouse_xp", base64_encode($_POST['lh_password']), time()+220752000, '/');
				}
				return true;
			} else {
				return false;
			}
		}
	}
	
	public function set_session($user_session, $key){
		$cookieData = serialize($user_session);
//		$cookieData = mcrypt_encrypt('MCRYPT_RIJNDAEL_256', 'lh', $cookieData, 'MCRYPT_MODE_ECB');
		$cookieData = base64_encode($cookieData);
		setcookie($key, $cookieData, 0, '/');
	}

	public function get_session($key){
		if(isset($_COOKIE[$key])){
			$login = $_COOKIE[$key];
			$login = base64_decode($login);
			$login_user_array = unserialize($login);
			foreach($login_user_array as $key => $value){
				$_SESSION[$key] = $value;
				$this->_session->$key = $value;
			}
		}
	}

	public function get_session_value($key){
		if(isset($_COOKIE[$key])){
			$cookie_value = $_COOKIE[$key];
			$cookie_value = base64_decode($cookie_value);
			$cookie_value = unserialize($cookie_value);			
		}
		return $cookie_value;
	}

	public function getBcProjects() {
		
	}
	
	public function bcXML($file, $user) {
		
		//echo "Ses Vars: (" .$this->_session->lh_username .")<br />";
		//echo "Ses Vars: (" .$this->_session->lh_password .")<br />";
		$out  = "GET $file HTTP/1.1\r\n";
		$out .= "Host: ".$this->config->basecamp->host."t\r\n";
		$out .= "Connection: Close\r\n";
		$out .= "Accept: application/xml\n";
		$out .= "Content-Type: application/xml\r\n";
		$out .= "Authorization: Basic ".base64_encode($user['lh_username'].":".$user['lh_password'])."\r\n";
		$out .= "\r\n";
	    //$conex = fsockopen("ssl://".$this->config->basecamp->host, $this->config->basecamp->port, $errno, $errstr, 10);
		
	    //Open port to basecamp
		if (!$conex = @fsockopen("ssl://".$this->config->basecamp->host, $this->config->basecamp->port, $errno, $errstr, 10)) {
			
			return 0;
		}else{
			//Gather data from basecamp connection
			fwrite($conex, $out);
			$data = '';
			
			while (!feof($conex)) {
			    $data .= fgets($conex, 512);
			}
			fclose($conex);
			//If user has been unauthorized
			if(strpos($data, "401 Unauthorized")) {
				return false;
			}else if(strpos($data, "Access denied")) {
				return false;
			}else if(strpos($data,"404 Not Found") && (strpos($data,'Check to make sure'))){
				return true;
			}else{
				return false;
			}
		}
	}
	
	/*************************************
	Old php_lib basecamp functions
	**************************************/
	function query($string)
	{
		return $this->mysql->query($string);
	}
	function get_person_extra_list($extra)
	{
		$e=array();
		$r=$this->query("SELECT DISTINCT $extra FROM person_extras");
		while($row=$r->fetch_row())
		{
			$e[]=$row[0];
		}
		return $e;
	}
	function get_person_extra($bc_id,$extra)
	{
		$r=$this->query("SELECT $extra FROM person_extras WHERE bc_id=".$this->e($bc_id));
		if($row=$r->fetch_row())
		{
			return($row[0]);
		}
		return false;
	}
	function get_bc_id_from_name($first_name,$last_name)
	{
		$r=$this->query("SELECT bc_id FROM person_basecamp WHERE first_name LIKE ".$this->eq($first_name)." AND last_name LIKE ".$this->qe($last_name));
		if($row=$r->fetch_row())
		{
			return $row[0];
		}
		else
		{
			return false;
		}
	}
	function get_bc_id_from_email($email)
	{
		$r=$this->query("SELECT bc_id FROM person_basecamp WHERE email LIKE ".$this->eq($email));
		if($row=$r->fetch_row())
		{
			return $row[0];
		}
		else
		{
			return false;
		}
	}
	function is_valid_company($company,$return_bc_id=false)
	{
		$query="SELECT bc_id FROM company_basecamp WHERE ";
		if(strlen($company)>=6 && !preg_match('/\D/',$company))
		{
			$query.='bc_id='.$this->e($company);
		}
		else
		{
			$query.='name='.$this->eq($company);
		}
		$r=$this->mysql->query($query)->fetch_row();
		if($r)
		{
			if($return_bc_id)
			{
				return $r[0];
			}
			else
			{
				return true;
			}
		}
		return false;
		
	}
	function is_valid_person_extra($type)
	{
		$query="SELECT count(id) FROM person_extras WHERE type=".$this->eq($type);
		$c=$this->mysql->query($query)->fetch_row();
		if($c[0]>0)
		{
			return true;
		}
		return false;
	}
	private function _start_running()
	{
		/*$this->mysql->query('LOCK TABLES 
							company_basecamp WRITE,
							company_basecamp AS cread READ,
							person_basecamp WRITE,
							person_basecamp AS pread READ,
							project_basecamp WRITE,
							project_basecamp AS prread READ
							');*/
	}
	private function _end_running()
	{
		$this->mysql->query('UNLOCK TABLES');
	}
	function get_all_users()
	{
		$r=$this->query("SELECT bc_id,email,first_name,last_name,phone_office,phone_office_ext,phone_mobile,phone_home,phone_fax,title,bc_uuid,im_handle,im_service FROM person_basecamp ORDER BY first_name");
		$users=array();
		while($row=$r->fetch_assoc())
		{
			$users[]=array(
				'email_address'=>$row['email'],
				'first-name'=>$row['first_name'],
				'last-name'=>$row['last_name'],
				'id'=>$row['bc_id'],
				'im-handle'=>$row['im_handle'],
				'im-service'=>$row['im_service'],
				'phone-number-fax'=>$row['phone_fax'],
				'phone-number-home'=>$row['phone_home'],
				'phone-number-mobile'=>$row['phone_mobile'],
				'phone-number-office'=>$row['phone_office'],
				'phone-number-office-ext'=>$row['phone_office_ext'],
				'title'=>$row['title'],
				'uuid'=>$row['bc_uuid'],
				);
		}
		return $users;
	}
	function get_all_projects()
	{
		$projects=array();
		$r=$this->query("SELECT p.bc_id,p.announcement,p.status,p.name,p.company_id,c.name FROM project_basecamp p, company_basecamp c WHERE p.company_id=c.bc_id");
		while($row=$r->fetch_row())
		{
			$projects[$row[0]]=array(
				'announcement'=>$row[1],
				'id'=>$row[0],
				'name'=>$row[3],
				'status'=>$row[2],
				'company'=>array('id'=>$row[4],'name'=>$row[5])
				);
		}
		return $projects;
	}
	function get_all_projects_by_company()
	{
		$cr=$this->query("SELECT DISTINCT name,bc_id FROM company_basecamp ORDER BY name");
		$pbc=array();
		while($c=$cr->fetch_assoc())
		{
			$projects=array();
			$pr=$this->query("SELECT name,status,bc_id FROM project_basecamp WHERE company_id=".$c['bc_id']." ORDER BY subname,name");
			while($p=$pr->fetch_assoc())
			{
				$projects[$p['name']]=array(
						'id'=>$p['bc_id'],
						'name'=>$p['name'],
						'status'=>$p['status'],
						'company'=>array('id'=>$c['bc_id'],'name'=>$c['name'])
					);
			}
			if(count($projects)) $pbc[$c['name']]=$projects;
		}
		return $pbc;
	}
	function get_all_active_projects_by_company()
	{
		$cr=$this->query("SELECT DISTINCT name,bc_id FROM company_basecamp ORDER BY name");
		$pbc=array();
		while($c=$cr->fetch_assoc())
		{
			$projects=array();
			$pr=$this->query("SELECT name,status,bc_id FROM project_basecamp WHERE company_id=".$c['bc_id']." ORDER BY subname,name");
			while($p=$pr->fetch_assoc())
			{
				if($p['status']=='active')
				{
					$projects[$p['bc_id']]=$p['name'];
				}
			}
			if(count($projects)) $pbc[$c['name']]=$projects;
		}
		return $pbc;
	}
	function get_project_brief_data_types()
	{
		$dt=array();
		$r=$this->query("SELECT id,name,description,color FROM project_brief_data_types ORDER BY ord,name");
		while($row=$r->fetch_assoc())
		{
			$n=preg_replace('/[^A-Za-z0-9 ]/','',$row['name']);
			$n=preg_replace('/\s+/','_',$n);
			$d=array('id'=>$row['id'],'description'=>$row['description'],'color'=>$row['color'],'htmlname'=>$n);
			$dt[$row['name']]=$d;
		}
		return $dt;
	}
	function get_all_projects_with_briefs()
	{
		$pwb=array();
		$r=$this->query("SELECT id,bc_id,total_budget,budget_code,start,end FROM project_brief ORDER BY bc_id");
		while($row=$r->fetch_assoc())
		{
			$pwb[$row['bc_id']]=$row;
		}
		return $pwb;
	}
	function cache()
	{
		$this->_start_running();
		$this->mysql->query('DELETE FROM company_basecamp');
		$this->mysql->query('DELETE FROM person_basecamp');
		$this->mysql->query('DELETE FROM project_basecamp');
		//filling company table
		$xml=$this->api('/contacts/companies');
		$stmt=$this->mysql->prepare('INSERT INTO company_basecamp (bc_id, name, address, address2, city, state, zip, country, client_of, web_address, phone_office, phone_fax,time_zone, bc_uuid) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
		foreach($xml as $c)
		{
			$zip=preg_replace('/\D/','',$c->zip);
			$po=preg_replace('/\D/','',$xml->{'phone-number-office'});
			$pf=preg_replace('/\D/','',$xml->{'phone-number-fax'});
			$stmt->bind_param('dsssssssdsssss',
				$c->id, 
				$c->name,
				$c->{'address-one'}, 
				$c->{'address-two'}, 
				$c->city, 
				$c->state, 
				$zip, 
				$c->country, 
				$c->{'client-of'}, 
				$c->{'web-address'}, 
				$po, 
				$pf, 
				$c->{'time-zone-id'}, 
				$c->uuid);
			$stmt->execute();
		}
		$stmt->close();
		//filling project table
		$xml=$this->api('/project/list');
		$stmt=$this->mysql->prepare('INSERT INTO project_basecamp (bc_id, company_id, status, name, announcement, uxd_code, subname) VALUES(?,?,?,?,?,?,?)');
		foreach($xml as $p)
		{
			$codes=preg_split('/\s*-\s*/',$p->name);
			$uxd_code=$codes[0];
			$uxd_code=trim($uxd_code);
			$uxd_code=preg_replace('/\s+.*$/','',$uxd_code);
			$stmt->bind_param('ddsssss',
				$p->id,
				$p->company->id,
				$p->status,
				$p->name,
				$p->announcement,
				$uxd_code,
				$codes[1]
			);
			$stmt->execute();
		}
		$stmt->close();

		//filling person table
		$id=array_shift($this->mysql->query('SELECT bc_id FROM company_basecamp WHERE name='.$this->eq(THIS_COMPANY))->fetch_row()); 
		$xml=$this->api('/contacts/people/'.$id);
		$stmt=$this->mysql->prepare('INSERT INTO person_basecamp (bc_id, email, first_name, last_name, phone_office, phone_office_ext, phone_mobile, phone_home, phone_fax, title, bc_uuid, im_handle, im_service, deleted) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
		foreach($xml as $p)
		{
			$po=preg_replace('/\D/','',$p->{'phone-number-office'});
			$poe=preg_replace('/\D/','',$p->{'phone-number-office-ext'});
			$pf=preg_replace('/\D/','',$p->{'phone-number-fax'});
			$pm=preg_replace('/\D/','',$p->{'phone-number-mobile'});
			$ph=preg_replace('/\D/','',$p->{'phone-number-home'});
			$deleted=0;
			if($p->deleted=='true') $deleted=1;
			$stmt->bind_param('dssssssssssssd',
				$p->id,
				$p->{'email-address'},
				$p->{'first-name'},
				$p->{'last-name'},
				$po, 
				$poe,
				$pm,
				$ph,
				$pf,
				$p->title,
				$p->uuid,
				$p->{'im-handle'},
				$p->{'im-service'},
				$deleted
			);
			$stmt->execute();
		}
		$this->_end_running();
	}
	function e($string)
	{
		return $this->mysql->escape_string($string);
	}
	function eq($string)
	{
		return "'".$this->mysql->escape_string($string)."'";
	}
	function api($api,$request='')
	{
		$url=BC_URL.$api;
		$ch = curl_init();
		$request='<request>'.$request.'</request>';
		curl_setopt($ch, CURLOPT_URL, $url);
		if(defined('HTTP_PROXY') && HTTP_PROXY!='')
		{
			curl_setopt($ch,CURLOPT_PROXY,HTTP_PROXY);
			curl_setopt($ch,CURLOPT_HTTPPROXYTUNNEL,true);
			curl_setopt($ch,CURLOPT_PROXYTYPE,CURLPROXY_HTTP);
		}
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt ($ch, CURLOPT_POST, 1);
		curl_setopt ($ch, CURLOPT_POSTFIELDS, $request);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/xml', 'Content-Type: application/xml'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch,CURLOPT_USERPWD,BC_USER . ":" . BC_PASSWORD);
		if(preg_match('/^(https)/',$url)) curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
		$response = curl_exec($ch);
		curl_close($ch);
		if($response && $response!='' && $response!=' ')
		{
			return simplexml_load_string($response);
		}
		else
		{
			return false;
		}
	}
}
?>
