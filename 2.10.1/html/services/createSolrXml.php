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
			
			$workorders_comment = "SELECT * FROM `workorder_comments` WHERE `workorder_id`='" .$workorders_row['id'] ."'";
			$workorders_comment_res = $mysql->query($workorders_comment);
			$workorders_comment_row = $workorders_comment_res->fetch_assoc();
			
			$doc = new DOMDocument(); 
			$doc->formatOutput = true; 
			$r = $doc->createElement( "add" ); 
			$doc->appendChild( $r ); 
			foreach( $workorders_row as $workorders_row ) 
			{
			$b = $doc->createElement( "doc" ); 
			$id = $doc->createElement( "id" ); 
			$id->appendChild( $doc->createTextNode( $workorders_row['id'] ) ); 
			$b->appendChild( $id ); 
			
			$guid = $doc->createElement( "guid" ); 
			$guid->appendChild( $doc->createTextNode( $workorders_row['project_id'] ) ); 
			$b->appendChild( $guid ); 
			
			
			$title = $doc->createElement( "title" ); 
			$title->appendChild( $doc->createTextNode( $workorders_row['title'] ) ); 
			$b->appendChild( $title ); 
			
			$title_facet = $doc->createElement( "title_facet" ); 
			$title_facet->appendChild( $doc->createTextNode( $workorders_row['example_url'] ) ); 
			$b->appendChild( $title_facet ); 
			
			
			
			$description = $doc->createElement( "description" ); 
			$description->appendChild( $doc->createTextNode( $workorders_row['body'] ) ); 
			$b->appendChild( $description );


			$categories = $doc->createElement( "categories" ); 
			$categories->appendChild( $doc->createTextNode( 'workorder' ) ); 
			$b->appendChild( $categories ); 
			
			
			$title = $doc->createElement( "title" ); 
			$title->appendChild( $doc->createTextNode( $workorders_row['title'] ) ); 
			$b->appendChild( $title );

			$author = $doc->createElement( "author" ); 
			$author->appendChild( $doc->createTextNode( $workorders_row['requested_by'] ) ); 
			$b->appendChild( $author ); 
			
			$r->appendChild( $b ); 
			} 
			
			echo $doc->saveXML(); 
			$doc->writeCData($doc);
			$doc->save("lh.xml");
					
			//return  $workorders_row;
		
	}}
			require_once('../_inc/config.inc');
			$c = new createSolrXml();
			$u = new stdClass();
	    	$w = new stdClass();
	    	 print_r($c->workorders());
			
?>