<?php 


class createSolrXml{

	private static $instance;
    private $count = 0;
	public $config;

	public static function singleton()
    {
        if (!isset(self::$instance)) {
            //echo 'Creating new instance.';
            $mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
            self::$instance = $mysql;
        }
        return self::$instance;
    }
	
	public function workorders(){
		
			$mysql = self::singleton();
			$workorders = "SELECT * FROM workorders";  
			$workorders_res = $mysql->query($workorders);
			$workorders_row = $workorders_res->fetch_assoc();
			return  $workorders_row;
			}
			}
			require_once('../_inc/config.inc');
			$c = new createSolrXml();
			$u = new stdClass();
	    	$w = new stdClass();
	    	echo workorders();
?>