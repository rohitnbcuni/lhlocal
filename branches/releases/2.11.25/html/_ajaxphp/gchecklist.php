<?php

include('../_inc/config.inc');
include("sessionHandler.php");
require_once 'Zend/Loader.php';

Zend_Loader::loadClass('Zend_Gdata_AuthSub');
Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
Zend_Loader::loadClass('Zend_Gdata_Spreadsheets');
Zend_Loader::loadClass('Zend_Gdata_Docs');
            
 
        //-------------------------------------------------------------------------------
       
        $wid = $_REQUEST['wid'];
        $username = GOOGLE_ACCOUNT; // Your google account username
        $password = GOOGLE_PASSWORD; // Your google account password
         
        //-------------------------------------------------------------------------------
        // Document key - get it from browser addres bar query key for your open spreadsheet
         
        $spreadsheetKey = $key = GOOGLE_SPREADSHEETKEY;
         
        //---------------------------------------------------------------------------------
        // Init Zend Gdata service
         
      /*  $service = Zend_Gdata_Spreadsheets::AUTH_SERVICE_NAME;
        $client = Zend_Gdata_ClientLogin::getHttpClient($username, $password, $service);
        $spreadsheetService = new Zend_Gdata_Spreadsheets($client);*/
         
     // connect to API
        $service = Zend_Gdata_Spreadsheets::AUTH_SERVICE_NAME;
        $client = Zend_Gdata_ClientLogin::getHttpClient($username, $password, $service);
        $service = new Zend_Gdata_Spreadsheets($client);  

        $listquery = new Zend_Gdata_Spreadsheets_ListQuery();
        $listquery->setSpreadsheetKey($key);
        //$query->setWorksheetId($worksheetId);
        $listquery->setSpreadsheetQuery("wid=$wid");
        //$listFeed = $spreadSheetService->getListFeed($query);
        $allReadyExist = array();
        $wid_rows = $service->getListFeed($listquery);
        $existRowId = 0;
        if (count($wid_rows->entry) == 0) {
            
        } else {
            $existRowId = 1;
            foreach ($wid_rows as $row) {
                    $rowData = $row->getCustom();
                    foreach($rowData as $customEntry) {
                        $allReadyExist[$customEntry->getColumnName()] = $customEntry->getText();
                    }
                  
            }
        }
       // p( $allReadyExist);     
    try {  
        
       

        // define worksheet query
        // get list feed for query
        $query = new Zend_Gdata_Spreadsheets_CellQuery();
        $query->setSpreadsheetKey($key);
        //$query->setWorksheetId('wsid');
        $query->setMinRow(1);
        $query->setMaxRow(1);
        $cellFeed = $service->getCellFeed($query);
    } catch (Exception $e) {
        die('ERROR: ' );
    }

    $spreadSheetColumnName = array(); 
    foreach($cellFeed as $cellEntry) {
        $spreadSheetColumnName[$cellEntry->cell->getColumn()] = $cellEntry->cell->getText();
    }
    //Get Cloumn
   // p($spreadSheetColumnName);
    $query = new Zend_Gdata_Spreadsheets_ListQuery();
    $query->setSpreadsheetKey($key);
    //$query->setWorksheetId($worksheetId);
    $listFeed = $service->getListFeed($query);
   
    $spreadSheetColumnIndex = array();
    $rowData = $listFeed->entries[1]->getCustom();
    $i = 1;
    foreach($rowData as $customEntry) {
        $spreadSheetColumnIndex[$customEntry->getColumnName()] = $spreadSheetColumnName[$i];
        $i++;
    }
    
    ?>
    <style>
    .mappProj{
		padding:10px;
		border:2px solid #0055A8;
		margin-top:37px;
		height:400px;
		overflow:auto;
	}
	.adminTable{
	
	
	
	}
	.adminTh{
		height:20px;
	
	}
	.adminTable th{
	background:none repeat scroll 0 0 #011548;
	color:#fff;
	width:40px;
	}
	.adminTable td{
	border:1px solid #0055A8;
	width:40px;
	text-align:left;
	}
	.adminTable .dropProject{
	cursor:pointer;
	}
    </style>
    <script src="//code.jquery.com/jquery-1.10.2.js"></script>
    
    <?php
    
        $wid = $_GET['wid'];
		if(!empty($wid)){
    
    ?>
    <form id="prechecklist">
        <div class="mappProj">
        <table class="adminTable" width="100%" name="mapProjectData">
            <tbody>
                
                 <?php
					global $mysql;
					$select_wo = "SELECT status FROM `workorders` WHERE `id`= ? LIMIT 1";
					$result = @$mysql->sqlprepare($select_wo,array($wid));
					$row = $result->fetch_assoc();
					$wid_status = $row['status']; 
                    
                    foreach($spreadSheetColumnIndex as $gkey => $gVal) :?>
                    <?php    if($gkey != 'wid'): ?>
                     <tr class="adminTh">   
                        <th colspan="3"><?php echo $gVal ?></th>
                    </tr>
					
                    <tr id="tr_<?php echo $gkey ?>" class="adminTr">
					<?php if($wid_status != '1') : ?>
						<?php 
                            $yChecked = '';
                            $nChecked = '';
                            $oChecked = '';
                            $others = '';
                           // echo "ss".$allReadyExist[$gkey];
                            if(ISSET($allReadyExist[$gkey])) {
                                if($allReadyExist[$gkey] == 'Yes'){
                                    $yChecked = "checked='checked'";
                                
                                
                                }else if($allReadyExist[$gkey] == 'No'){
                                    $nChecked = "checked='checked'";
                                
                                
                                }else {
                                    $oChecked = "checked='checked'";
                                    $others = $allReadyExist[$gkey];
                                
                                }

                            }
                        ?>
                        <td width="30%">
                        
                        <input type="radio" name="<?php echo $gkey ?>"  value="Yes" <?php echo $yChecked ?>> Yes
                        </td>
                        <td width="30%"><input type="radio" name="<?php echo $gkey ?>" value="No" <?php echo $nChecked ?> >No</td>
                        <td width="30%">
                            <input type="radio" name="<?php echo $gkey ?>" value="Other" <?php echo $oChecked ?> onclick="removeReadOnly('<?php echo $gkey ?>_text');">Other
                            <input type="text" id="<?php echo $gkey ?>_text" name="<?php echo $gkey ?>_text" maxlength="100" readonly="readonly" value="<?php echo $others ?>" >
                        </td>
						<?php else : ?>
						<td width="30%">
                        
                         <?php echo $yChecked ?>
                        </td>
                        <td colspan="3">
						<?php echo $allReadyExist[$gkey] ?>
                        </td>
						<?php endif ; ?>
						
						
                    </tr>
                    
                <?php 
                    endif;
                endforeach; ?>
                <tr>
                     
                    <td colspan="3"> 
                    <input type="hidden" name="wid" maxlength="100" value="<?php echo $wid?>" >
                    <?php if($existRowId == 1){ ?>
                    <input type="hidden" name="editwid" maxlength="100" value="edit" >
                    <input type="button" onclick="showValues();" value="Update" />
                    <?php }else{ ?>
                    <input type="button" onclick="showValues();" value="submit" />
                     <?php } ?>
                </tr>
                
            </tbody>
        </table>
       </div>
    </form>
    <?php } ?>
    <script>
  function showValues() {
    var str = $( "#prechecklist" ).serialize();
       $.ajax({
            type: "POST",
            url: "<?php echo BASE_URL ?>/_ajaxphp/gchecklistPost.php",
            data: str,
            success: function(data) {
               alert("Information has been saved");
                //var obj = jQuery.parseJSON(data); if the dataType is not specified as json uncomment this
                // do what ever you want with the server response
            },
            error: function(){
                  alert('error handing here');
            }
        });
    }
    
    function removeReadOnly(id){
    
        $('#'+id).removeAttr("readonly");
    
    }
 
 
</script>
       
    
        
        
        
        