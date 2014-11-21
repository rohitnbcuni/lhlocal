<?php


include('../_inc/config.inc');
include("sessionHandler.php");
require_once 'Zend/Loader.php';

Zend_Loader::loadClass('Zend_Gdata_AuthSub');
Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
Zend_Loader::loadClass('Zend_Gdata_Spreadsheets');
Zend_Loader::loadClass('Zend_Gdata_Docs');

$username = GOOGLE_ACCOUNT; // Your google account username
$password = GOOGLE_PASSWORD; // Your google account password
 
//-------------------------------------------------------------------------------
// Document key - get it from browser addres bar query key for your open spreadsheet
 
$spreadsheetKey = $key = GOOGLE_SPREADSHEETKEY;

 try {  
        // connect to API
        $service = Zend_Gdata_Spreadsheets::AUTH_SERVICE_NAME;
        $client = Zend_Gdata_ClientLogin::getHttpClient($username, $password, $service);
        $service = new Zend_Gdata_Spreadsheets($client);
       // $ret = $service->insertRow($testData, $key);
       
        $query = new Zend_Gdata_Spreadsheets_ListQuery();
        $query->setSpreadsheetKey($key);
        //$query->setWorksheetId($worksheetId);
        $listFeed = $service->getListFeed($query);
   
        $spreadSheetColumnIndex = array();
        $rowData = $listFeed->entries[1]->getCustom();
        //$i = 1;
        foreach($rowData as $customEntry) {
            $spreadSheetColumnIndex[] = $customEntry->getColumnName();
           // $i++;
        }
        $finalPostArray = array();
        foreach($spreadSheetColumnIndex as $gKey => $gVal){
            //if(strpos($_POST[$gVal],"_text") > 0){
                if(($_POST[$gVal] == 'Other')){
                    $finalPostArray[$gVal] = $_POST[$gVal."_text"];
                
                }else{
            
                    $finalPostArray[$gVal] = $_POST[$gVal];
            
                }
                
        }
        if(ISSET($_POST['editwid'])){
            $listquery = new Zend_Gdata_Spreadsheets_ListQuery();
            $listquery->setSpreadsheetKey($key);
            //$query->setWorksheetId($worksheetId);
            $wid = $_POST['wid'];
            $listquery->setSpreadsheetQuery("wid=$wid");
            $wid_rows = $service->getListFeed($listquery);
           
            foreach ($wid_rows as $row) {
                $rowData = $row->getCustom();
                $updatedListEntry = $service->updateRow($row,$finalPostArray);
                  
            }
            
            
        
        }else{        
            $ret = $service->insertRow($finalPostArray, $key);
        }
        
        
    } catch (Exception $e) {
        die('ERROR: ' . $e->getMessage());
    }
    
   


?>