<?php
include('../_inc/config.inc');
	@session_start();

	if(!ISSET($_SESSION['user_id'])){
		header("Location:../login/");
	}
	
	$root_path = $_SERVER['DOCUMENT_ROOT'];
	$file_path = $root_path."/".($_GET['path']);
	$filename = basename($file_path);	
	$fname = basename($file_path);
	if (!is_file($file_path)) {
		header("Location:../noaccess/"); 
	}else{
		$p = urldecode($_GET['path']);
		header("Location:".BASE_URL."/".$p);
	}
	//disable force download LH#28470
	//$fileDown = new PHPFileDownload(null,null,$filename,null,$file_path);
   // $fileDown->exportData();
    
	 class PHPFileDownload {

    var $headers;
    var $fileName;
    var $data;
    var $fileType;
    var $filePath;

    
    /*
     * see above examples to see how data is passed
     */
    function __construct($headers = null,$data = null,$fileName = null,$fileType = null,$filePath = null) {

        if($headers == null) {
            $this->setHeader(array());
        }else {
            $this->setHeader($headers);
        }
        if($fileName == null) {
            $this->setFileName(date("Y-m-d_H:i:s")); // default file name
        }else {
            $this->setFileName($fileName);
        }

        if($data  == null) {
            $this->setData(array());
        }else {
            $this->setData($data);
        }

        if($fileType == null) {
            $this->setFileType("csv"); // default file type
        }else {
            $this->setFileType($fileType);
        }

        if($filePath != null) {
            $this->setFilePath($filePath); 
            $this->setFileType("file");

        }
    }
    
    public function setFileName($fileName) {
        $this->fileName = $fileName;
    }

    public function getFileName() {

        $fileType = $this->getFileType();
        switch ($fileType ) {
            case 'text/csv'                     :   $this->fileName = $this->fileName . ".csv";
                                                    break;
                                                
            case 'application/vnd.ms-excel'     :   $this->fileName = $this->fileName . ".xls";

            case 'file'                         :   $this->fileName = basename($this->getFilePath());
                                                    break;

            default                             :   echo "Invalid File Type";
                                                    break;
        }

        return $this->fileName;
    }
    public function setHeader($headers) {
        $this->headers = $headers;
    }

    public function getHeader() {
        
        $fileType = $this->getFileType();
        switch ($fileType ) {
            case 'text/csv'                     :   $this->headers = '"' . implode('","',$this->headers) . '"';
                                                    break;

            case 'application/vnd.ms-excel'     :   $this->headers = '<tr><th>' . implode('</th><th>',$this->headers) . '<th></tr>';
                                                    break;

            case 'file'                         :   $this->headers = '';
                                                    break;

            default:                                echo "Invalid File Type";
                                                    break;
        }
        return $this->headers;

    }

    public function setFileType($fileType) {

        switch ($fileType) {
            case  'csv' :   $this->fileType =  'text/csv';
                            break;

            case  'xls' :   $this->fileType =  'application/vnd.ms-excel';
                            break;

             case 'file':   $this->fileType = filetype($this->getFilePath());
                            break;
                        
            default:        echo "Invalid File Type";
                            break;
        }
    }

    public function getFileType() {
        return $this->fileType;
    }
    public function setData($data) {
        $this->data = $data;
    }

    public function getData() {
        return $this->data;
    }

    public function setFilePath($path) {
        $this->filePath = $path;
    }

    public function getFilePath() {
        return $this->filePath;
    }
    /*
     * Creates a csv string if you want to use another
     * character instead of , change the $seperator variable
     */
    public function getCsvDataString() {
      
        $csvData = "";
        $seperator = '","';

        foreach ($this->data as $row) {
            foreach($row as $key=>&$value) {
                $value = str_replace('"', '""', $value); // handle quotes inside fields
            }
            $csvData  =  $csvData . '"' . implode($seperator,$row) . '"'. "\n";
        }

        return $csvData;
    }

     /*
     * Microsoft excel has the ability to undestand HTML table
     * so we create an html table and save it as a filename.xls
     */
    
    public function getXlsDataString() {
        
        foreach ($this->data as $row) {
            $table = $table.  '<tr><td>' . implode('</td><td>',$row) . '</td></tr>';

        }
        return $table;
    }
   

    public function exportData(){

        if($this->getFileType() == 'text/csv') {
            $dataContents = $this->getHeader() . "\n" . $this->getCsvDataString();
            $this->executeHeaders();
            echo $dataContents;
            
        }elseif($this->getFileType() == 'application/vnd.ms-excel') {
            $dataContents = "<table border =1>";
            $dataContents = $dataContents . $this->getHeader() . $this->getXlsDataString();
            $dataContents =  $dataContents.  "</table>";
            $this->executeHeaders();
            echo $dataContents;


        }elseif($this->getFileType() == 'file') {
            $this->executeHeaders();
            set_time_limit(0);
            readfile($this->getFilePath());
            
        }
        
        
	}

    public function executeHeaders() {

        header("Pragma: hack");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private", false);
        header("Content-Type: " . $this->getFileType());
        header("Content-type: application/octet-stream");
        header('Content-Disposition: attachment; filename="' . $this->getFileName() . '";');
        header("Content-Transfer-Encoding: binary");
    }


}

?>
