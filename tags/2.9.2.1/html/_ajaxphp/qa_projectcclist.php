<?PHP
        session_start();
        include('../_inc/config.inc');
		include("sessionHandler.php");
                $pattern = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
        if(isset($_SESSION['user_id'])) {
                $mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);

                $user = $_SESSION['lh_username'];
                $password = $_SESSION['lh_password'];

                $project_id = $mysql->real_escape_string($_GET['project_id']);
                              
                if(!empty($project_id))
                {
                $wo_query = "SELECT `qccclist` FROM `projects` WHERE `id`='$project_id' LIMIT 1";
				$wo_result = $mysql->query($wo_query);
                $wo_row = $wo_result->fetch_assoc();
				$list = explode(",", $wo_row[qccclist]);
			 $listu .= '<input type="hidden" name="temp_cc_list" id="temp_cc_list" value="'.$wo_row[qccclist].'">';
                for($x = 0; $x < sizeof($list); $x++) {
                if(!empty($list[$x])) {
				
                                        $select_cc_user = "SELECT * FROM `users` WHERE `id`='" .$list[$x] ."' LIMIT 1";
                                        $cc_user_result = @$mysql->query($select_cc_user);
                                        $cc_user_row = @$cc_user_result->fetch_assoc();

                                        $listu .= "<li>"
                                                ."<div class=\"cclist_name\">" .ucfirst($cc_user_row['first_name']) ." " .ucfirst($cc_user_row['last_name']) ."</div>"
                                                ."<button class=\"status cclist_remover\" onClick=\"removeqcCcUser(" .$new_list[$x] ."); return false;\"><span>remove</span></button>"
                                                ."</li>";
                                     
                        
                }
                }
echo $listu;
				}}

?>


