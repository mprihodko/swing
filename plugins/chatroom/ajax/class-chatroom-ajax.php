<?php

class chatroom_Ajax {

    public $ajax_actions;
	public $is_buddypress;
	public $is_bpfriend;
    /*
     * Configuring and intializing ajax files and actions
     *
     * @param  -
     * @return -
     */

    public function __construct() {
		
		if(session_id() == '') {
			session_start();
		}
		$this->check_buddypress();
		$data = get_option('chatroom_options');
		$this->is_bpfriend = $data['only_bp_friend'];
        //$this->configure_actions();
        //add_action('wp_enqueue_scripts', array($this, 'include_scripts'));
    }

    public function initialize() {
        $this->configure_actions();
    }

    /*
     * Confire the application specific AJAX actions array and
     * load the AJAX actions bases on supplied parameters
     *
     * @param  -
     * @return -
     */

    public function configure_actions() {

        $this->ajax_actions = array(
			"cr_register_user" => array("action" => "cr_register_user_action", "function" => "cr_register_user_function"),
			"cr_refresh_friends" => array("action" => "cr_refresh_friends_action", "function" => "cr_refresh_friends_function"),
			"cr_online_friends" => array("action" => "cr_online_friends_action", "function" => "cr_online_friends_function"),
			"cr_bp_create_group" => array("action" => "cr_bp_create_group_action", "function" => "cr_bp_create_group_function"),
			"cr_get_group_chat_row" => array("action" => "cr_get_group_chat_row_action", "function" => "cr_get_group_chat_row_function"),
			"cr_get_private_chat_row" => array("action" => "cr_get_private_chat_row_action", "function" => "cr_get_private_chat_row_function"),
			"cr_bp_group_list" => array("action" => "cr_bp_group_list_action", "function" => "cr_bp_group_list_function"),
			"cr_bp_group_member_list" => array("action" => "cr_bp_group_member_list_action", "function" => "cr_bp_group_member_list_function"),
			"cr_search_friends" => array("action" => "cr_search_friends_action", "function" => "cr_search_friends_function"),
			"cr_load_chat_row" => array("action" => "cr_load_chat_row_action", "function" => "cr_load_chat_row_function"),
			"cr_load_commonchat_row" => array("action" => "cr_load_commonchat_row_action", "function" => "cr_load_commonchat_row_function"),
			"cr_submit_commonroom_message" => array("action" => "cr_submit_commonroom_message_action", "function" => "cr_submit_commonroom_message_function"),
			"cr_submit_group_message" => array("action" => "cr_submit_group_message_action", "function" => "cr_submit_group_message_function"),
			"cr_submit_private_message" => array("action" => "cr_submit_private_message_action", "function" => "cr_submit_private_message_function"),
			"cr_upload_image" => array("action" => "cr_upload_image_action", "function" => "cr_upload_image_function"),
			"cr_upload_file" => array("action" => "cr_upload_file_action", "function" => "cr_upload_file_function"),
			"cr_upload_video" => array("action" => "cr_upload_video_action", "function" => "cr_upload_video_function"),
        );

        /*
         * Add the AJAX actions into WordPress
         */
        foreach ($this->ajax_actions as $custom_key => $custom_action) {

            if (isset($custom_action["logged"]) && $custom_action["logged"]) {
                // Actions for users who are logged in
                add_action("wp_ajax_" . $custom_action['action'], array($this, $custom_action["function"]));
            } else if (isset($custom_action["logged"]) && !$custom_action["logged"]) {
                // Actions for users who are not logged in
                add_action("wp_ajax_nopriv_" . $custom_action['action'], array($this, $custom_action["function"]));
            } else {
                // Actions for users who are logged in and not logged in
                add_action("wp_ajax_nopriv_" . $custom_action['action'], array($this, $custom_action["function"]));
                add_action("wp_ajax_" . $custom_action['action'], array($this, $custom_action["function"]));
            }
        }
    }
	
    /*
     * Register new application user from frontend
     *
     * @param  -
     * @return void
     */

    public function cr_register_user_function() {
		header("Content-Type: application/json");
		$message = array();
		$a = &$message;
		$a["cr_message"] = '';
		$a["cr_chatbox"] = '';
		$a["cr_error"] = false;	
		$cr_errors = false;
		
		$data = get_option('chatroom_options');		
		$email_exists = !empty($data['cr_lg_email_exists'])?$data['cr_lg_email_exists']:'User with this email already registered.';
		$invalid_email = !empty($data['cr_lg_invalid_user'])?$data['cr_lg_invalid_user']:'Invalid email address.';
		$invalid_user = !empty($data['cr_lg_invalid_user'])?$data['cr_lg_invalid_user']:'Invalid username.';
		$user_exits = !empty($data['cr_lg_user_exits'])?$data['cr_lg_user_exits']:'Username alreay exists.';
		$reg_failed = !empty($data['cr_lg_reg_failed'])?$data['cr_lg_reg_failed']:'Registration failed.';
		$reg_success = !empty($data['cr_lg_reg_success'])?$data['cr_lg_reg_success']:'Successful! Please wait...';
		
		$user_login = isset ( $_POST['cr_signup_username'] )? $_POST['cr_signup_username'] : '' ;
		$user_email = isset ( $_POST['cr_signup_email'] )? $_POST['cr_signup_email'] : '' ;
		$display_name = isset ( $_POST['cr_signup_fullname'] )? $_POST['cr_signup_fullname'] : '' ;
		$user_pass = isset ( $_POST['cr_signup_password'] )? $_POST['cr_signup_password'] : '' ;
		$pass_confirm = isset ( $_POST['cr_signup_password_confirm'] )? $_POST['cr_signup_password_confirm'] : '' ;
		
		
		$user_type  = 'subscriber';
		$split_name = explode(" ",$display_name);
		if(count($split_name) >= 3){
			$first_name = $split_name[0].' '.$split_name[1];
			$last_name = '';
			for($i = 2; $i < count($split_name); $i++) {
				$last_name = $split_name[i];
			}
		}else if(count($split_name) == 2){
			$first_name = $split_name[0];
			$last_name =$split_name[1];
		}else{
			$first_name = $split_name[0];
			$last_name ='';
		}
		// Validating user data

		if( $user_pass != $pass_confirm){
			$a["cr_message"] .= '<span>'.$pass_check.'</span><br/>';
			$cr_errors = true;
			$a["cr_error"] = true;
		}

		$sanitized_user_login = sanitize_user($user_login);

		if (!is_email($user_email)){
			$a["cr_message"] .= '<span>'.$invalid_email.'</span><br/>';
			$cr_errors = true;
			$a["cr_error"] = true;
		}
		
		if (email_exists($user_email)){
			$a["cr_message"] .= '<span>'.$email_exists.'</span><br/>';
			$cr_errors = true;
			$a["cr_error"] = true;
		}
		if (!validate_username($user_login)){
			$a["cr_message"] .= '<span>'.$invalid_user.'</span><br/>';
			$cr_errors = true;
			$a["cr_error"] = true;
		}
		
		if (username_exists($user_login)){
			$a["cr_message"] .= '<span>'.$user_exits.'</span><br/>';
			$cr_errors = true;
			$a["cr_error"] = true;
		}
		if (!$cr_errors) {
			
			$user_id = wp_insert_user(array('user_login' => $sanitized_user_login,
						'user_email' => $user_email,
						'display_name' => $display_name,
						'role' => $user_type,
						'first_name' => $first_name,
						'last_name' => $last_name,
						'user_pass' => $user_pass));


			if (!$user_id) {
				$a["cr_message"] .= '<span class="cr_error">'.$reg_failed.'</span>';
				$a["cr_error"] = true;
			} else {
				$activation_code = $this->random_string();

				update_user_meta($user_id, 'activation_code', $activation_code);
				update_user_meta($user_id, 'activation_status', "active");
				//wp_new_user_notification($user_id, $user_pass, $activation_code);
				
				$a["cr_message"] .= '<span class="cr_success">'.$reg_success.'</span>';
				$a["cr_username"] = $sanitized_user_login;
				$a["cr_password"] = $user_pass;
				$a["cr_error"] = false;
			}

		}
		
		echo json_encode($message);
		exit;
    }
	
	/*
     * chat_user_online functions for checking user online or offline
     *
     * @param  -
     * @return -
     */
	 
	public function chat_user_online($user_id, $time=5){
			global $wpdb;
			$sql = $wpdb->prepare( "
				SELECT u.user_login FROM $wpdb->users u 
				WHERE u.ID = %d
				AND DATE_ADD( u.chatroom_last_activity, INTERVAL %d MINUTE ) >= UTC_TIMESTAMP()", $user_id, $time);
			$user_login = $wpdb->get_var( $sql );
			if(isset($user_login) && $user_login !=""){
				return true;
			}
			else {return false;}
	}
	
	/*
     * is_buddypress functions for checking buddypress installation status
     *
     * @param  -
     * @return -
     */
	 
	public function check_buddypress(){
		global $wpdb;
		$isbp = $wpdb->get_results( "SELECT id FROM {$wpdb->prefix}bp_friends ORDER BY id LIMIT 1");
		if(!empty($isbp)){
			$this->is_buddypress = true;
		}else{
			$this->is_buddypress = false;
		}
	}
	
    /*
     * cr_refresh_friends functions for handling AJAX request
     *
     * @param  -
     * @return -
     */

    public function cr_refresh_friends_function() {
		header("Content-Type: application/json");
		$chat = array();
		$a = &$chat;
		$a["FriendsRow"] = '';
		
		$data = get_option('chatroom_options');
		$no_result = !empty($data['cr_lg_no_result'])?$data['cr_lg_no_result']:'No results.';
		
		global $wpdb;
		$wpdb->show_cr_errors = true;
				
		$UserId = get_current_user_id();
		
		$FriendsSQL = $wpdb->get_results($wpdb->prepare("SELECT id, display_name FROM $wpdb->users WHERE id NOT LIKE '%d' ORDER BY id LIMIT 50", $UserId));		

		if(!empty($FriendsSQL)){
			foreach($FriendsSQL as $Row) {
				$ID = $Row->id;
				$string = stripslashes(htmlspecialchars($Row->display_name));
				$DisplayName = (mb_strlen($string) > 17) ? mb_substr($string,0,15).'..' : $string;
				//$getAvater = str_replace('&','&amp;',get_avatar($Row->id));
				$src = $this->get_user_image_src($Row->id);
				
				$online = $this->chat_user_online($ID)==true?'chat_online':'chat_offline';
								
				$a["FriendsRow"] .= '<div data-event="cr-private-chat-init" data-parameter-user-name="'.$DisplayName.'" data-parameter-user-id="'.$ID.'" class="chatroomFriendsRow cr_clear"><i class="chatStatus '.$online.'_circle"></i><img class="chatroomFriendsImage '.$online.'" src="'.$src.'" /><div class="chatroomFriendsName">'.$DisplayName.'</div></div>';
			}
		}else{
			$a["FriendsRow"] .= "<center style=\"margin: 10px\">".$no_result."</center>";
		}
		echo json_encode($chat);
		
		exit;
    }

    /*
     * cr_online_friends functions for handling AJAX request
     *
     * @param  -
     * @return -
     */

    public function cr_online_friends_function() {
		header("Content-Type: application/json");
		$chat = array();
		$a = &$chat;
		$a["FriendsRow"] = null;
		
		$data = get_option('chatroom_options');
		$no_result = !empty($data['cr_lg_no_result'])?$data['cr_lg_no_result']:'No results.';
		$nom_online = !empty($data['cr_lg_nom_online'])?$data['cr_lg_nom_online']:'No member online.';
		
		global $wpdb;
		$wpdb->show_cr_errors = true;
				
		$UserId = get_current_user_id();
		$time = 5;
		
		$FriendsSQL = $wpdb->get_results($wpdb->prepare("SELECT id, display_name FROM $wpdb->users WHERE id NOT LIKE '%d' AND DATE_ADD( chatroom_last_activity, INTERVAL %d MINUTE ) >= UTC_TIMESTAMP() ORDER BY id LIMIT 50", $UserId, $time));			
						
		
		if(!empty($FriendsSQL)){
			foreach($FriendsSQL as $Row) {
				$ID = $Row->id;
				$string = stripslashes(htmlspecialchars($Row->display_name));
				$DisplayName = (mb_strlen($string) > 20) ? mb_substr($string,0,17).'...' : $string;
				$src = $this->get_user_image_src($Row->id);
				
				//$online = $this->chat_user_online($ID)==true?'chat_online':'chat_offline';
				
				if($this->chat_user_online($ID)==true){
					
					$a["FriendsRow"] .= '<div data-event="cr-private-chat-init" data-parameter-user-name="'.$DisplayName.'" data-parameter-user-id="'.$ID.'" class="chatroomFriendsRow cr_clear"><i class="chatStatus chat_online_circle"></i><img class="chatroomFriendsImage chat_online" src="'.$src.'" /><div class="chatroomFriendsName">'.$DisplayName.'</div></div>';
				}
			}
		}
		
		if($a["FriendsRow"] == null)
			$a["FriendsRow"] .= "<center style=\"margin: 10px\">".$nom_online."</center>";
		else if(empty($FriendsSQL))
			$a["FriendsRow"] .= "<center style=\"margin: 10px\">".$no_result."</center>";
			
		echo json_encode($chat);
		exit;
    }
	
    /*
     * bp_group_list functions for handling AJAX request
     *
     * @param  -
     * @return -
     */

    public function cr_bp_group_list_function() {
		header("Content-Type: application/json");
		$chat = array();
		$a = &$chat;
		$a["FriendsRow"] = '';
		$data = get_option('chatroom_options');
		$no_result = !empty($data['cr_lg_no_result'])?$data['cr_lg_no_result']:'No results.';
		global $wpdb;
		$wpdb->show_cr_errors = true;
				
		$UserId = get_current_user_id();
	
		$FriendsSQL = $wpdb->get_results($wpdb->prepare("SELECT g.id AS id, g.name AS name FROM {$wpdb->prefix}bp_groups g, {$wpdb->prefix}bp_groups_members gm WHERE g.id = gm.group_id AND gm.user_id = '%d' AND gm.is_confirmed = 1 ORDER BY g.id LIMIT 50", $UserId));		
		if(!empty($FriendsSQL)){
			foreach($FriendsSQL as $Row) {
				$ID = $Row->id;
				$string = stripslashes(htmlspecialchars($Row->name));
				$GroupName = (mb_strlen($string) > 17) ? mb_substr($string,0,15).'..' : $string;
								
				$a["FriendsRow"] .= '<div data-event="cr_bp_group_member_list" data-parameter-group-name="'.$GroupName.'" data-parameter-group-id="'.$ID.'" class="chatroomFriendsRow cr_clear"><div class="chatroomFriendsName">'.$GroupName.'</div></div>';
								
			}
		}else{
			$a["FriendsRow"] .= "<center style=\"margin: 10px\">".$no_result."</center>";
		}
		
		echo json_encode($chat);
		exit;
    }

    /*
     * cr_bp_group_member_list functions for handling AJAX request
     *
     * @param  -
     * @return -
     */

    public function cr_bp_group_member_list_function() {
		header("Content-Type: application/json");
		$chat = array();
		$a = &$chat;
		$a["FriendsRow"] = null;
		
		$data = get_option('chatroom_options');
		$no_result = !empty($data['cr_lg_no_result'])?$data['cr_lg_no_result']:'No results.';
		$nom_online = !empty($data['cr_lg_nom_online'])?$data['cr_lg_nom_online']:'No member online.';
		
		global $wpdb;
		$wpdb->show_cr_errors = true;
				
		$UserId = get_current_user_id();
		$GroupID = $_POST["GroupID"];
		
		$FriendsSQL = $wpdb->get_results($wpdb->prepare("SELECT u.id AS id, u.display_name AS display_name FROM $wpdb->users u, {$wpdb->prefix}bp_groups_members gm WHERE u.id NOT LIKE '%d' AND gm.user_id = u.id AND gm.group_id = '%d' ORDER BY u.id LIMIT 50", $UserId, $GroupID));		
		if(!empty($FriendsSQL)){
			foreach($FriendsSQL as $Row) {
				$ID = $Row->id;
				$string = stripslashes(htmlspecialchars($Row->display_name));
				$DisplayName = (mb_strlen($string) > 17) ? mb_substr($string,0,15).'..' : $string;
				$src = $this->get_user_image_src($Row->id);
				
				//$online = $this->chat_user_online($ID)==true?'chat_online':'chat_offline';
				
				if($this->chat_user_online($ID)==true){					
					$a["FriendsRow"] .= '<div data-event="cr-private-chat-init" data-parameter-user-name="'.$DisplayName.'" data-parameter-user-id="'.$ID.'" class="chatroomFriendsRow cr_clear"><i class="chatStatus chat_online_circle"></i><img class="chatroomFriendsImage chat_online" src="'.$src.'" /><div class="chatroomFriendsName">'.$DisplayName.'</div></div>';
					
				}
			}
		}
		
		
		if($a["FriendsRow"] == null)
			$a["FriendsRow"] .= "<center style=\"margin: 10px\">".$nom_online."</center>";
		else if(empty($FriendsSQL))
			$a["FriendsRow"] .= "<center style=\"margin: 10px\">".$no_result."</center>";
		
		echo json_encode($chat);
		exit;
    }
		
    /*
     * cr_search_friends functions for handling AJAX request
     *
     * @param  -
     * @return -
     */

    public function cr_search_friends_function() {
		header("Content-Type: application/json");
		$chat = array();
		$a = &$chat;
		$a["FriendsRow"] = '';
		$data = get_option('chatroom_options');
		$no_result = !empty($data['cr_lg_no_result'])?$data['cr_lg_no_result']:'No results.';
		global $wpdb;
		$wpdb->show_cr_errors = true;
		
		$searchData = $_POST["cr_searchData"];
		
		$UserId = get_current_user_id();
	
		
		$FriendsSQL = $wpdb->get_results("SELECT id, display_name FROM $wpdb->users WHERE display_name LIKE '%".esc_sql($searchData)."%' AND id NOT LIKE '".$UserId."' ORDER BY RAND(id) LIMIT 50");
		if(!empty($FriendsSQL)){			
			foreach($FriendsSQL as $Row) {
				$ID = $Row->id;
				$string = stripslashes(htmlspecialchars($Row->display_name));
				$DisplayName = (mb_strlen($string) > 17) ? mb_substr($string,0,15).'..' : $string;
				$src = $this->get_user_image_src($Row->id);
				
				$online = $this->chat_user_online($ID)==true?'chat_online':'chat_offline';
								
				$a["FriendsRow"] .= '<div data-event="cr-private-chat-init" data-parameter-user-name="'.$DisplayName.'" data-parameter-user-id="'.$ID.'" class="chatroomFriendsRow cr_clear"><i class="chatStatus '.$online.'_circle"></i><img class="chatroomFriendsImage '.$online.'" src="'.$src.'" /><div class="chatroomFriendsName">'.$DisplayName.'</div></div>';
			}
		}else{
			$a["FriendsRow"] .= "<center style=\"margin: 10px\">".$no_result."</center>";
		}
		
		echo json_encode($chat);
		exit;
    }

    /*
     * cr_load_chat_row functions for handling AJAX request
     *
     * @param  -
     * @return -
     */

   public function cr_load_chat_row_function() {
		header("Content-Type: application/json");
		$chat = array();
		$a = &$chat;
		$a["cr_private_senderinfo"] = array();		
		$a["cr_private_chatinfo"] = array();
		$cr_last_common_chatid = $_POST["cr_last_common_chatid"];
		$cr_last_groupid_chatid = $_POST["cr_last_groupid_chatid"];
		//$time = $_POST["cr_msg_time"];
		
        $chatAray = array();
	
		global $wpdb;
		$wpdb->show_cr_errors = true;
		
		$UserId = get_current_user_id();
		
		$Read = 0;
		$is_commonroom = 0;
		$is_buddypress = 0;
		$is_private = 1;
		$bp_group_id =0;

		$senderQuery = $wpdb->get_results($wpdb->prepare("SELECT DISTINCT u.display_name AS display_name, c.user_sender AS user_sender FROM {$wpdb->prefix}chatroom_message AS c JOIN $wpdb->users AS u ON u.ID = c.user_sender WHERE c.user_receiver = '%d' AND c.chat_read = '%d' ORDER BY c.id DESC LIMIT 15", $UserId, $Read));
		if(count($senderQuery) > 0) {
			foreach($senderQuery as $senderRow) {
				$friendID = $senderRow->user_sender;
				$string = stripslashes(htmlspecialchars($senderRow->display_name));
				$friendName = (mb_strlen($string) > 16) ? mb_substr($string,0,13).'...' : $string;
				$src1 = $this->get_user_image_src($friendID);
				
				$a["cr_private_senderinfo"][$friendID] = array("SenderID" => $friendID, 
														"SenderName" => $friendName, 
														"avatar"=> $src1
														);
																					
				$MessageSQL = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}chatroom_message WHERE user_receiver = '%d' AND user_sender = '%d' AND is_commonroom = '%d' AND is_buddypress = '%d' AND is_private = '%d' AND bp_group_id = '%d' AND chat_read = '%d' ORDER BY id ASC LIMIT 15", $UserId, $friendID, $is_commonroom, $is_buddypress, $is_private, $bp_group_id, $Read));
								
				foreach($MessageSQL as $Row) {
					$chatID = $Row->id;
					$chatAray[] = $Row->id;
					$senderID = $Row->user_sender;
					$receiverID = $Row->user_receiver;
					$chat_time = $Row->chat_time;
					$message = stripslashes($Row->message);
					
					$src = $this->get_user_image_src($senderID);
					
					$a["cr_private_chatinfo"][$chatID] = array("chatid" => $chatID, 
														"senderid" => $senderID, 
														"receiverid" => $receiverID, 
														"message"=> $message,
														"chat_time"=> $chat_time,
														"avatar"=> $src,
														);
					
				}
			}
		}
		
		
		$is_commonroom1 = 1;
		$is_buddypress1 = 0;
		$is_private1 = 0;
		$bp_group_id1 =0;
		
		if($cr_last_common_chatid != 0){
			$CommonSQL = $wpdb->get_results($wpdb->prepare("SELECT u.display_name AS display_name, c.id AS id, c.user_sender AS user_sender, c.chat_time AS chat_time, c.message AS message FROM {$wpdb->prefix}chatroom_message AS c JOIN $wpdb->users AS u ON u.ID = c.user_sender WHERE c.user_sender != '%d' AND c.is_commonroom = '%d' AND c.is_buddypress = '%d' AND c.is_private = '%d' AND c.bp_group_id = '%d' AND c.id > '%d' ORDER BY c.id ASC LIMIT 15", $UserId, $is_commonroom1, $is_buddypress1, $is_private1, $bp_group_id1, $cr_last_common_chatid));
						
		}else{
			$CommonSQL = $wpdb->get_results($wpdb->prepare("SELECT  u.display_name AS display_name, c.id AS id, c.user_sender AS user_sender, c.chat_time AS chat_time, c.message AS message FROM {$wpdb->prefix}chatroom_message AS c JOIN $wpdb->users AS u ON u.ID = c.user_sender WHERE c.user_sender != '%d' AND c.is_commonroom = '%d' AND c.is_buddypress = '%d' AND c.is_private = '%d' AND c.bp_group_id = '%d' ORDER BY c.id ASC LIMIT 15", $UserId, $is_commonroom1, $is_buddypress1, $is_private1, $bp_group_id1));
		}
		
		if(count($CommonSQL) > 0) {
			foreach($CommonSQL as $Row) {
				$chatID = $Row->id;
				$senderID = $Row->user_sender;
				$senderName = $Row->display_name;
				$receiverID = $Row->user_receiver;
				$chat_time = $Row->chat_time;
				$message = stripslashes($Row->message);
				
				$src = $this->get_user_image_src($senderID);
				
				$a["cr_common_chatinfo"][$chatID] = array("chatid" => $chatID, 
													"senderid" => $senderID, 
													"senderName" => $senderName,
													"receiverid" => $receiverID, 
													"message"=> $message,
													"chat_time"=> $chat_time,
													"avatar"=> $src,
													);
				
			}
		}
		
		
		if($cr_last_groupid_chatid != 0){
			$is_commonroom2 = 0;
			$is_buddypress2 = 1;
			$is_private2 = 0;
			
			$gid = explode(";",$cr_last_groupid_chatid);
			
			for($i = 0; $i < count($gid); $i++) {
				$gcid = explode(",",$gid[$i]);
				$bp_group_id2 = $gcid[0];
				$last_chat_id2 = $gcid[1];
				
				$GroupSQL = $wpdb->get_results($wpdb->prepare("SELECT u.display_name AS display_name, c.id AS id, c.user_sender AS user_sender, c.chat_time AS chat_time, c.message AS message FROM {$wpdb->prefix}chatroom_message AS c JOIN $wpdb->users AS u ON u.ID = c.user_sender WHERE c.user_sender != '%d' AND c.is_commonroom = '%d' AND c.is_buddypress = '%d' AND c.is_private = '%d' AND c.bp_group_id = '%d' AND c.id > '%d' ORDER BY c.id ASC LIMIT 15", $UserId, $is_commonroom2, $is_buddypress2, $is_private2, $bp_group_id2, $last_chat_id2));
							
				if(count($GroupSQL) > 0) {
					foreach($GroupSQL as $Row) {
						$chatID = $Row->id;
						$senderID = $Row->user_sender;
						$senderName = $Row->display_name;
						$receiverID = $Row->user_receiver;
						$chat_time = $Row->chat_time;
						$message = stripslashes($Row->message);
						
						$src = $this->get_user_image_src($senderID);
						
						$a["cr_group_chatinfo"][$chatID] = array("chatid" => $chatID, 
															"senderid" => $senderID, 
															"senderName" => $senderName,
															"receiverid" => $receiverID, 
															"message"=> $message,
															"chat_time"=> $chat_time,
															"avatar"=> $src,
															"groupid"=> $bp_group_id2,
															);
						
					}
				}
			}
		}
		if(count($chatAray) > 0) {
			foreach($chatAray as $key=>$id){
				$wpdb->update( 
					$wpdb->prefix.'chatroom_message',
					array( 'chat_read' => 1),
					array( 'id' => $id ),
					array( '%d'),
					array( '%d')
				);
			}
		}
		//date_default_timezone_set('asia/dhaka');
		$date = date('Y-m-d H:i:s');
		//update_user_meta( $UserId, 'last_activity', $date );
		//update_user_meta( $user_id, $meta_key, $meta_value, $prev_value );
		
		//also working
		
		$wpdb->update( 
			$wpdb->users,
			array( 'chatroom_last_activity' => $date),
			array( 'ID' => $UserId )
		);
		
		$a['cr_userid'] = $UserId;
		echo json_encode($chat);
		exit;
    }
	
	public function cr_load_commonchat_row_function() {
		header("Content-Type: application/json");
		$chat = array();
		$a = &$chat;
		
		global $wpdb;
		$wpdb->show_cr_errors = true;
		$UserId = get_current_user_id();
		$is_commonroom = 1;
		$is_buddypress = 0;
		$is_private = 0;
		$bp_group_id =0;
		
		$MessageSQL = $wpdb->get_results($wpdb->prepare("SELECT u.display_name AS display_name, c.id AS id, c.user_sender AS user_sender, c.chat_time AS chat_time, c.message AS message FROM {$wpdb->prefix}chatroom_message AS c JOIN $wpdb->users AS u ON u.ID = c.user_sender WHERE is_commonroom = '%d' AND is_buddypress = '%d' AND is_private = '%d' AND bp_group_id = '%d' ORDER BY id ASC LIMIT 15", $is_commonroom, $is_buddypress, $is_private, $bp_group_id));

		foreach($MessageSQL as $Row) {
			$chatID = $Row->id;
			$senderID = $Row->user_sender;
			$senderName = $Row->display_name;
			$chat_time = $Row->chat_time;
			$message = stripslashes($Row->message);
			$src = $this->get_user_image_src($senderID);
			
			if($senderID == $UserId){
				$is_user = true;
			}else{
				$is_user = false;
			}
			
			$a["cr_chatinfo"][$chatID] = array("chatid" => $chatID, 
												"senderid" => $senderID, 
												"senderName" => $senderName,
												"message"=> $message,
												"chat_time"=> $chat_time,
												"avatar"=> $src,
												"is_user"=> $is_user,
												);
			
		}
		
		$a['cr_userid'] = $UserId;
		
		echo json_encode($chat);
        exit;
		
	}
	    
	 /*
     * cr_submit_commonroom_message functions for handling AJAX request
     *
     * @param  -
     * @return -
     */

    public function cr_submit_commonroom_message_function() {

        header("Content-Type: application/json");
		
		$chat = array();
		$a = &$chat;
		
		global $wpdb;
		$wpdb->show_cr_errors = true;
		
		$message = $_POST["chatroom_message"];
		$time = $_POST["cr_msg_time"];
		$UserId = get_current_user_id();
		$user_receiver = 0;
		$is_commonroom = 1;
		$is_buddypress = 0;
		$is_private = 0;
		$bp_group_id = 0;
			
		$row = $wpdb->insert( 
			$wpdb->prefix.'chatroom_message', 
			array( 
				'user_sender' => $UserId, 
				'user_receiver' => $user_receiver,
				'message' => $message,
				'is_commonroom' => $is_commonroom,
				'is_buddypress' => $is_buddypress,
				'is_private' => $is_private,
				'bp_group_id' => $bp_group_id
			), 
			array( 
				'%d', 
				'%d',
				'%s',
				'%d',
				'%d',
				'%d',
				'%d'
			) 
		);
		
		$a['row_time'] = $time;
		
		if($row){
			$a['is_insert'] = 1;
		}else{
			$a['is_insert'] = 0;
		}
		
		echo json_encode($chat);
        exit;
    }

	 /*
     * cr_submit_group_message functions for handling AJAX request
     *
     * @param  -
     * @return -
     */

    public function cr_submit_group_message_function() {
        header("Content-Type: application/json");
		
		$chat = array();
		$a = &$chat;
		
		global $wpdb;
		$wpdb->show_cr_errors = true;
		
		$message = $_POST["cr_group_message"];
		$bp_group_id = $_POST["cr_groupid"];
		$UserId = get_current_user_id();
		$user_receiver = 0;
		$is_commonroom = 0;
		$is_buddypress = 1;
		$is_private = 0;
			
		$row = $wpdb->insert( 
			$wpdb->prefix.'chatroom_message', 
			array( 
				'user_sender' => $UserId, 
				'user_receiver' => $user_receiver,
				'message' => $message,
				'is_commonroom' => $is_commonroom,
				'is_buddypress' => $is_buddypress,
				'is_private' => $is_private,
				'bp_group_id' => $bp_group_id
			), 
			array( 
				'%d', 
				'%d',
				'%s',
				'%d',
				'%d',
				'%d',
				'%d'
			) 
		);
		
		$a['row_time'] = $time;
		
		if($row){
			$a['is_insert'] = 1;
		}else{
			$a['is_insert'] = 0;
		}
		
		echo json_encode($chat);
        exit;
    }

	 /*
     * cr_submit_private_message functions for handling AJAX request
     *
     * @param  -
     * @return -
     */

    public function cr_submit_private_message_function() {

        header("Content-Type: application/json");
		
		$chat = array();
		$a = &$chat;
		
		global $wpdb;
		$wpdb->show_cr_errors = true;
		
		$message = $_POST["cr_private_message"];
		$user_receiver = $_POST["cr_receiver"];
		$UserId = get_current_user_id();
		$is_commonroom = 0;
		$is_buddypress = 0;
		$is_private = 1;
		$bp_group_id =0;
		
		$row = $wpdb->insert( 
			$wpdb->prefix.'chatroom_message', 
			array( 
				'user_sender' => $UserId, 
				'user_receiver' => $user_receiver,
				'message' => $message,
				'is_commonroom' => $is_commonroom,
				'is_buddypress' => $is_buddypress,
				'is_private' => $is_private,
				'bp_group_id' => $bp_group_id
			), 
			array( 
				'%d', 
				'%d',
				'%s',
				'%d',
				'%d',
				'%d',
				'%d'
			) 
		);
		
		$a['row_time'] = $time;
		
		if($row){
			$a['is_insert'] = 1;
		}else{
			$a['is_insert'] = 0;
		}
		
		echo json_encode($chat);
        exit;
    }
	
	 /*
     * cr_bp_create_group functions for handling AJAX request
     *
     * @param  -
     * @return -
     */

    public function cr_bp_create_group_function() {

        header("Content-Type: application/json");
		
		$chat = array();
		$a = &$chat;
		
		global $wpdb;
		$wpdb->show_cr_errors = true;
									
		$cr_UserId = $_POST["cr_userid"];
		$cr_groupName = $_POST["cr_groupName"];
		$cr_groupDesc = $_POST["cr_groupDesc"];
		$cr_privacy = $_POST["cr_privacy"];
		$cr_access = $_POST["cr_access"];
		$slug = $this->slugify($cr_groupName);
		$create_date = date('Y-m-d H:i:s');
		$UserId = get_current_user_id();
			
		$insert = $wpdb->insert( 
			$wpdb->prefix.'bp_groups', 
			array( 
				'creator_id' => $UserId, 
				'name' => $cr_groupName,
				'slug' => $slug,
				'description' => $cr_groupDesc,
				'status' => $cr_privacy, 
				'enable_forum' => 0,
				'date_created' => $create_date
			), 
			array( 
				'%d', 
				'%s',
				'%s',
				'%s',
				'%s',
				'%s'
			) 
		);
		
		if($insert){
			$group_id = $wpdb->insert_id;
			$date_modified = date('Y-m-d H:i:s');
			$wpdb->insert( 
				$wpdb->prefix.'bp_groups_members', 
				array( 
					'group_id' => $group_id, 
					'user_id' => $UserId,
					'inviter_id' => 0,
					'is_admin' => 1,
					'user_title' => 'Group Admin',
					'is_confirmed' => 1,
					'is_admin' => 1,
					'date_modified' => $date_modified
				), 
				array( 
					'%d', 
					'%d',
					'%d',
					'%d',
					'%s',
					'%d',
					'%s'
				) 
			);
			$wpdb->insert( 
				$wpdb->prefix.'bp_groups_groupmeta', 
				array( 
					'group_id' => $group_id, 
					'meta_key' => 'invite_status', 
					'meta_value' => $cr_access
				), 
				array( 
					'%d',
					'%s',
					'%s'
				) 
			);
			$wpdb->insert( 
				$wpdb->prefix.'bp_groups_groupmeta', 
				array( 
					'group_id' => $group_id, 
					'meta_key' => 'total_member_count', 
					'meta_value' => 1
				), 
				array( 
					'%d',
					'%s',
					'%d'
				) 
			);
			$wpdb->insert( 
				$wpdb->prefix.'bp_groups_groupmeta', 
				array( 
					'group_id' => $group_id, 
					'meta_key' => 'last_activity', 
					'meta_value' => $date_modified
				), 
				array( 
					'%d',
					'%s',
					'%s'
				) 
			);
		}
		
		if($insert){
			$a['is_insert'] = 1;
		}else{
			$a['is_insert'] = 0;
		}
		
		echo json_encode($chat);
        exit;
    }
	
	 /*
     * cr_upload_image_function functions for handling AJAX request
     *
     * @param  -
     * @return -
     */
	
	public function cr_upload_image_function(){
		header("Content-Type: application/json");
		
		$chat = array();
		$a = &$chat;
		
		global $wpdb;
		$wpdb->show_cr_errors = true;
		
		$data = get_option('chatroom_options');
		$db_error = !empty($data['cr_lg_db_error'])?$data['cr_lg_db_error']:'Database Error';
		$mx_image = !empty($data['cr_lg_mx_image'])?$data['cr_lg_mx_image']:'Max image size is 4MB.';
		$allow_image = !empty($data['cr_lg_allow_image'])?$data['cr_lg_allow_image']:'Only jpeg, jpg, gif, png and pjpeg image is allowed.';
		
		$cr_type = $_POST["cr_window_type"];
		$user_id = $_POST["cr_upload_id"];
		
		$a['cr_time'] = $_POST["cr_time"];
		$a['cr_error'] = false;
    	$a['cr_error_data'] = '';
		
		$allowedExts = array("jpeg", "jpg", "gif", "png", "pjpeg");
		$allowedType = array("image/jpeg", "image/jpg", "image/gif", "image/png", "image/pjpeg");
		//require the needed files
		require_once(ABSPATH . "wp-admin" . '/includes/image.php');
		require_once(ABSPATH . "wp-admin" . '/includes/file.php');
		require_once(ABSPATH . "wp-admin" . '/includes/media.php');
		//then loop over the files that were sent and store them using  media_handle_upload();
		if ($_FILES) {
			foreach ($_FILES as $file => $array) {
				
				if ($_FILES[$file]['error'] !== UPLOAD_ERR_OK) {
					$a['cr_error_data'] .= "upload error : " . $_FILES[$file]['error']." ";
					$a['cr_error'] = true;
				}
				if ($_FILES[$file]['size'] > 4000000) {
					$a['cr_error_data'] .= $mx_image;
					$a['cr_error'] = true;
				}
				$fileType = $_FILES[$file]['type'];
				$fileExts = end(explode(".", $_FILES[$file]['name']));
				
				if (in_array($fileType, $allowedType)) {
					// do nothing
				}else{
					$a['cr_error_data'] .= $allow_image;
					$a['cr_error'] = true;
				}
				if($a['cr_error'] == false){
					$attachment_id = media_handle_upload( $file, 0 );
					$image = wp_get_attachment_image( $attachment_id, 'full', false, array('class' => "cr_image_center") );
					$image_src = wp_get_attachment_image_src( $attachment_id ); // returns an array
					if($cr_type == 'chatroom'){
						$row = $this->cr_insert_image_database($image, $user_receiver = 0, $is_commonroom = 1, $is_buddypress = 0, $is_private = 0, $bp_group_id = 0);
						if($row){
							$a['cr_insert_image'] = $image_src[0];
						}else{
							$a['cr_error_data'] .= $db_error;
							$a['cr_error'] = true;
						}
					}elseif($cr_type == 'group'){
						$row = $this->cr_insert_image_database($image, $user_receiver = 0, $is_commonroom = 0, $is_buddypress = 1, $is_private = 0, $bp_group_id = $user_id);
						if($row){
							$a['cr_insert_image'] = $image_src[0];
						}else{
							$a['cr_error_data'] .= $db_error;
							$a['cr_error'] = true;
						}
					}elseif($cr_type == 'private'){
						$row = $this->cr_insert_image_database($image, $user_receiver = $user_id, $is_commonroom = 0, $is_buddypress = 0, $is_private = 1, $bp_group_id = 0);
						if($row){
							$a['cr_insert_image'] = $image_src[0];
						}else{
							$a['cr_error_data'] .= $db_error;
							$a['cr_error'] = true;
						}
					}
					
				}
				
			}   
		}else{
			$a['cr_error_data'] .= 'No file found. ';
			$a['cr_error'] = true;
		}
		
		echo json_encode($chat);
        exit;
	}

	 /*
     * cr_upload_file_function functions for handling AJAX request
     *
     * @param  -
     * @return -
     */
	
	public function cr_upload_file_function(){
		header("Content-Type: application/json");
		
		$chat = array();
		$a = &$chat;
		
		global $wpdb;
		$wpdb->show_cr_errors = true;
		
		$data = get_option('chatroom_options');
		$db_error = !empty($data['cr_lg_db_error'])?$data['cr_lg_db_error']:'Database Error';
		$mx_file = !empty($data['cr_lg_mx_file'])?$data['cr_lg_mx_file']:'Max file size is 12MB.';
		$allowed_file = !empty($data['cr_lg_allowed_file'])?$data['cr_lg_allowed_file']:'File formet is not allowed.';
			
		$cr_type = $_POST["cr_window_type"];
		$user_id = $_POST["cr_upload_id"];
		
		$a['cr_time'] = $_POST["cr_time"];
		$a['cr_error'] = false;
    	$a['cr_error_data'] = '';
		
		$allowedExts = array("pdf", "doc", "docx", "xls", "xlsx", "csv", "txt", "rtf", "html", "zip", "mp3", "mp4", "avi", "odt", "ppt", "pptx", "pps", "ppsx");
		//require the needed files
		require_once(ABSPATH . "wp-admin" . '/includes/image.php');
		require_once(ABSPATH . "wp-admin" . '/includes/file.php');
		require_once(ABSPATH . "wp-admin" . '/includes/media.php');
		//then loop over the files that were sent and store them using  media_handle_upload();
		if ($_FILES) {
			foreach ($_FILES as $file => $array) {
				
				if ($_FILES[$file]['error'] !== UPLOAD_ERR_OK) {
					$a['cr_error_data'] .= "upload error : " . $_FILES[$file]['error']." ";
					$a['cr_error'] = true;
				}
				if ($_FILES[$file]['size'] > 12000000) {
					$a['cr_error_data'] .= $mx_file;
					$a['cr_error'] = true;
				}
				$fileExts = end(explode(".", $_FILES[$file]['name']));
				
				if (in_array($fileExts, $allowedExts)) {
					// do nothing
				}else{
					$a['cr_error_data'] .= $allowed_file;
					$a['cr_error'] = true;
				}
				if($a['cr_error'] == false){
					$attachment_id = media_handle_upload( $file, 0 );
					$file_link = wp_get_attachment_link( $attachment_id );
					$file_url = wp_get_attachment_url( $attachment_id ); // returns an array
					
					if($cr_type == 'chatroom'){
						$row = $this->cr_insert_image_database($file_link, $user_receiver = 0, $is_commonroom = 1, $is_buddypress = 0, $is_private = 0, $bp_group_id = 0);
						if($row){
							$a['cr_insert_file'] = $file_link;
						}else{
							$a['cr_error_data'] .= $db_error;
							$a['cr_error'] = true;
						}
					}elseif($cr_type == 'group'){
						$row = $this->cr_insert_image_database($file_link, $user_receiver = 0, $is_commonroom = 0, $is_buddypress = 1, $is_private = 0, $bp_group_id = $user_id);
						if($row){
							$a['cr_insert_file'] = $file_link;
						}else{
							$a['cr_error_data'] .= $db_error;
							$a['cr_error'] = true;
						}
					}elseif($cr_type == 'private'){
						$row = $this->cr_insert_image_database($file_link, $user_receiver = $user_id, $is_commonroom = 0, $is_buddypress = 0, $is_private = 1, $bp_group_id = 0);
						if($row){
							$a['cr_insert_file'] = $file_link;
						}else{
							$a['cr_error_data'] .= $db_error;
							$a['cr_error'] = true;
						}
					}
					
				}
			}   
		}else{
			$a['cr_error_data'] .= $db_error;
			$a['cr_error'] = true;
		}
		
		echo json_encode($chat);
        exit;
	}
	
	 /*
     * cr_upload_file_function functions for handling AJAX request
     *
     * @param  -
     * @return -
     */
	
	public function cr_upload_video_function(){
		header("Content-Type: application/json");
		
		$chat = array();
		$a = &$chat;
		
		global $wpdb;
		$wpdb->show_cr_errors = true;
		$data = get_option('chatroom_options');
		$db_error = !empty($data['cr_lg_db_error'])?$data['cr_lg_db_error']:'Database Error';
		
		$cr_videoid = $_POST["cr_videoid"];
		$cr_type = $_POST["cr_window_type"];
		$user_id = $_POST["cr_upload_id"];
		$a['cr_time'] = $_POST["cr_time"];
		$a['cr_error'] = false;
    	$a['cr_error_data'] = '';
		$a['cr_videoid'] = '';
		
		$youtube = '<div class="cr_video" style="min-width:300px;min-height:180px; margin:0 auto;"><iframe title="YouTube video player" src="http://www.youtube.com/embed/'.$cr_videoid.'" frameborder="0" allowfullscreen></iframe></div>';
		
		if($cr_type == 'chatroom'){
			$row = $this->cr_insert_image_database($youtube, $user_receiver = 0, $is_commonroom = 1, $is_buddypress = 0, $is_private = 0, $bp_group_id = 0);
			if($row){
				$a['cr_videoid'] = $cr_videoid;
			}else{
				$a['cr_error_data'] .= $db_error;
				$a['cr_error'] = true;
			}
		}elseif($cr_type == 'group'){
			$row = $this->cr_insert_image_database($youtube, $user_receiver = 0, $is_commonroom = 0, $is_buddypress = 1, $is_private = 0, $bp_group_id = $user_id);
			if($row){
				$a['cr_videoid'] = $cr_videoid;
			}else{
				$a['cr_error_data'] .= $db_error;
				$a['cr_error'] = true;
			}
		}elseif($cr_type == 'private'){
			$row = $this->cr_insert_image_database($youtube, $user_receiver = $user_id, $is_commonroom = 0, $is_buddypress = 0, $is_private = 1, $bp_group_id = 0);
			if($row){
				$a['cr_videoid'] = $cr_videoid;
			}else{
				$a['cr_error_data'] .= $db_error;
				$a['cr_error'] = true;
			}
		}
		
		echo json_encode($chat);
        exit;
	}
	
		
	public function cr_insert_image_database($message, $user_receiver = 0, $is_commonroom = 0, $is_buddypress = 0, $is_private = 0, $bp_group_id = 0){
		
		global $wpdb;
		$wpdb->show_cr_errors = true;
		$UserId = get_current_user_id();
					
		$row = $wpdb->insert( 
			$wpdb->prefix.'chatroom_message', 
			array( 
				'user_sender' => $UserId, 
				'user_receiver' => $user_receiver,
				'message' => $message,
				'is_commonroom' => $is_commonroom,
				'is_buddypress' => $is_buddypress,
				'is_private' => $is_private,
				'bp_group_id' => $bp_group_id
			), 
			array( 
				'%d', 
				'%d',
				'%s',
				'%d',
				'%d',
				'%d',
				'%d'
			) 
		);
		
		return $row;
	}

	 /*
     * cr_get_group_chat_row_function functions for handling AJAX request
     *
     * @param  -
     * @return -
     */

    public function cr_get_group_chat_row_function() {

        header("Content-Type: application/json");
		
		$chat = array();
		$a = &$chat;
		
		global $wpdb;
		$wpdb->show_cr_errors = true;
				
		$bp_group_id = $_POST["cr_groupid"];
		$UserId = get_current_user_id();
		$is_commonroom = 0;
		$is_private = 0;
		$is_buddypress = 1;
		
		$MessageSQL = $wpdb->get_results($wpdb->prepare("SELECT u.display_name AS display_name, c.id AS id, c.user_sender AS user_sender, c.chat_time AS chat_time, c.message AS message FROM {$wpdb->prefix}chatroom_message AS c JOIN $wpdb->users AS u ON u.ID = c.user_sender WHERE is_commonroom = '%d' AND is_buddypress = '%d' AND is_private = '%d' AND bp_group_id = '%d' ORDER BY id ASC LIMIT 15", $is_commonroom, $is_buddypress, $is_private, $bp_group_id));

		foreach($MessageSQL as $Row) {
			$chatID = $Row->id;
			$senderID = $Row->user_sender;
			$senderName = $Row->display_name;
			$chat_time = $Row->chat_time;
			$message = stripslashes($Row->message);
			$src = $this->get_user_image_src($senderID);
			if($senderID == $UserId){
				$is_user = true;
			}else{
				$is_user = false;
			}
			
			$a["cr_group_chat_row"][$chatID] = array("chatid" => $chatID, 
												"senderid" => $senderID, 
												"senderName" => $senderName,
												"message"=> $message,
												"chat_time"=> $chat_time,
												"avatar"=> $src,
												"is_user" => $is_user,
												"groupid" => $bp_group_id,
												);
			
		}
		
		echo json_encode($chat);
        exit;
    }
	

	 /*
     * cr_get_private_chat_row_function functions for handling AJAX request
     *
     * @param  -
     * @return -
     */

    public function cr_get_private_chat_row_function() {

        header("Content-Type: application/json");
		
		$chat = array();
		$a = &$chat;
		$a["cr_private_chat_row"] = array();
        $chatAray = array();
		
		global $wpdb;
		$wpdb->show_cr_errors = true;
		
		$senderid = $_POST["cr_userid"];
		$UserId = get_current_user_id();
		$is_commonroom = 0;
		$is_private = 1;
		$is_buddypress = 0;
		$bp_group_id =0;
		
		$MessageSQL = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}chatroom_message WHERE (user_receiver = '%d' AND user_sender = '%d') or (user_receiver = '%d' AND user_sender = '%d') AND is_commonroom = '%d' AND is_private = '%d' AND is_buddypress = '%d' AND bp_group_id = '%d' ORDER BY id ASC LIMIT 15", $UserId, $senderid, $senderid, $UserId, $is_commonroom, $is_private, $is_buddypress, $bp_group_id));
		foreach($MessageSQL as $Row) {
			$chatID = $Row->id;
			$chatAray[] = $Row->id;
			$user_sender = $Row->user_sender;
			$user_receiver = $Row->user_receiver;
			$chat_time = $Row->chat_time;
			$message = stripslashes($Row->message);
			$src = $this->get_user_image_src($user_sender);
			if($user_sender == $UserId){
				$is_user = true;
			}else{
				$is_user = false;
			}
			
			$a["cr_private_chat_row"][$chatID] = array("chatid" => $chatID, 
												"senderid" => $user_sender, 
												"receiverid" => $user_receiver, 
												"message"=> $message,
												"chat_time"=> $chat_time,
												"avatar"=> $src,
												"is_user" => $is_user,
												"window_id" => $senderid,
												);
			
		}
		
		if(count($chatAray) > 0) {
			foreach($chatAray as $key=>$id){
				$wpdb->update( 
					$wpdb->prefix.'chatroom_message',
					array( 'chat_read' => 1),
					array( 'id' => $id ),
					array( '%d'),
					array( '%d')
				);
			}
		}
		
		echo json_encode($chat);
        exit;
    }

	
	public function get_user_image_src($UserId){
				
		$image_src = wp_get_attachment_image_src( get_usermeta($UserId,'cr_thumbnail_id',true )); 
		
		if($image_src[0]){
			return $image_src[0];
		}else{
			$avatar_url = get_avatar($UserId);
			$regex = '/(^.*src="|" w.*$)/';				
			$src = str_replace('&','&amp;',preg_replace($regex, '', $avatar_url));
					
			return $src;
		}
		
	}
	
	/*
     * Generate random string for activation code
     *
     * @param  -
     * @return string
     */

    public function random_string() {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randstr = '';
        for ($i = 0; $i < 15; $i++) {
            $randstr .= $characters[rand(0, mb_strlen($characters))];
        }
        return $randstr;
    }
	
	public function slugify($text){ 
	  // replace non letter or digits by -
	  $text = preg_replace('~[^\\pL\d]+~u', '-', $text);
	
	  // trim
	  $text = trim($text, '-');
	
	  // transliterate
	  $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
	
	  // lowercase
	  $text = strtolower($text);
	
	  // remove unwanted characters
	  $text = preg_replace('~[^-\w]+~', '', $text);
	
	  if (empty($text)){
		return 'n-a';
	  }
	
	  return $text;
	}
	
}

?>
