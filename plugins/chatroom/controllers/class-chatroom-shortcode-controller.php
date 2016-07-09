<?php

class Chatroom_Shortcode_Controller {
	
	private $user_id;

    public function __construct() {
		
		$this->user_id = get_current_user_id();
		
		add_filter('the_content', array($this, 'shortcodes_formatter'));
		add_filter('widget_text', array($this, 'shortcodes_formatter'));
		add_shortcode('chatroom', array($this, 'shortcode_chatroom'));
		
    }
	
	public function shortcodes_formatter($content) {
		
		$block = join("|",array("inner"));
		// opening tag
		$rep = preg_replace("/(<p>)?\[($block)(\s[^\]]+)?\](<\/p>|<br \/>)?/","[$2$3]",$content);
		// closing tag
		$rep = preg_replace("/(<p>)?\[\/($block)](<\/p>|<br \/>)/","[/$2]",$rep);
		return $rep;
		
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
	
	public function shortcode_chatroom($atts) {
		$atts = shortcode_atts(
			array(
				'id' => '',
			), $atts);
		
		$data = get_option('chatroom_options');
		$pls_login = !empty($data['cr_lg_pls_login'])?$data['cr_lg_pls_login']:'Please login to chat';
		$common_room = !empty($data['cr_lg_common_room'])?$data['cr_lg_common_room']:'Common chat room';
		$group_chat = !empty($data['cr_lg_group_chat'])?$data['cr_lg_group_chat']:'Group chat';
		$private_chat = !empty($data['cr_lg_private_chat'])?$data['cr_lg_private_chat']:'Private chat';
		$profile = !empty($data['cr_lg_profile'])?$data['cr_lg_profile']:'Profile';
		$submit = !empty($data['cr_lg_submit'])?$data['cr_lg_submit']:'Submit';
		
		$getAvater = str_replace('&','&amp;',get_avatar(get_current_user_id()));
		$doc = new DOMDocument();
		$doc->loadHTML($getAvater);
		$xpath = new DOMXPath($doc);
		$src = $xpath->evaluate("string(//img/@src)");
		$user_id = get_current_user_id();
		$user = get_userdata( $user_id );
		$name = $user->display_name;
				
		$chatroom .= '<div class="chatroomWindow cr_relative">';
			$chatroom .= '<div class="chatroomHeader">';
				$chatroom .= '<ul class="cr-head-tabs">';
					//$chatroom .= '<li data-event="cr-head-tabs" title="Profile"><img src="'.MCR_URL.'images/home.png"/></li>';
					
					$chatroom .= '<li data-event="cr-head-tabs" class="cr-current" title="'.$common_room.'">
					<svg version="1.1" class="cr_svg_icon" id="cr_network_layer" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px"
						 y="0px" width="24px" height="24px" viewBox="1 1 24 24" enable-background="new 1 1 24 24" xml:space="preserve">
					<g>
						<g>
							<path fill="#757575" d="M20.102,9.902c-0.195,0-0.391,0.017-0.58,0.043c-0.17-3.093-2.73-5.547-5.861-5.547
								c-2.561,0-4.507,1.861-5.313,4.144c-0.43-0.111-1.108-0.398-1.573-0.398c-2.923,0-5.293,2.371-5.293,5.295
								c0,2.924,2.371,5.294,5.293,5.294c0.144,0,0.307,0,0.48,0c-0.603,1.093-0.356,2.497,0.647,3.306
								c0.463,0.374,1.049,0.582,1.644,0.582c0.799,0,1.542-0.357,2.042-0.978c0.566-0.7,0.718-1.604,0.47-2.411
								c0.285,0.074,0.58,0.111,0.879,0.111c0.57,0,1.127-0.14,1.623-0.399c0.004,0.661,0.293,1.308,0.836,1.745
								c0.398,0.32,0.904,0.5,1.418,0.5l0,0c0.686,0,1.328-0.305,1.758-0.842c0.371-0.459,0.541-1.031,0.492-1.617
								c0.443,0,0.801,0,1.039,0c2.439,0,4.418-1.979,4.418-4.418C24.521,11.879,22.541,9.902,20.102,9.902z M17.922,19.824
								c-0.496,0.611-1.389,0.707-2,0.215c-0.404-0.326-0.578-0.827-0.506-1.306c0.025-0.185,0.086-0.367,0.188-0.534l-0.693-0.564
								c-0.91,1.004-2.428,1.162-3.528,0.373l-0.583,0.723c0.001,0.002,0.003,0.003,0.003,0.003c0.643,0.636,0.713,1.667,0.133,2.386
								c-0.618,0.77-1.74,0.887-2.509,0.268c-0.765-0.621-0.885-1.74-0.266-2.507c0.043-0.056,0.096-0.1,0.142-0.146
								c0.604-0.593,1.544-0.687,2.25-0.204l0.583-0.723c-0.994-0.912-1.147-2.426-0.354-3.521l-0.706-0.576
								c-0.438,0.38-1.098,0.403-1.562,0.027c-0.523-0.423-0.606-1.19-0.183-1.715c0.423-0.524,1.191-0.606,1.715-0.182
								c0.498,0.401,0.591,1.11,0.239,1.628l0.7,0.569c0.909-0.984,2.413-1.14,3.503-0.358l0.559-0.691
								c-0.547-0.542-0.615-1.42-0.119-2.036c0.529-0.656,1.49-0.757,2.145-0.229c0.654,0.531,0.756,1.489,0.227,2.144
								c-0.494,0.612-1.357,0.737-2.002,0.322l-0.563,0.691c0.994,0.905,1.154,2.415,0.377,3.509l0.689,0.56
								c0.504-0.522,1.334-0.587,1.906-0.122c0.293,0.235,0.463,0.563,0.512,0.906C18.271,19.108,18.178,19.506,17.922,19.824z"/>
						</g>
					</g>
					</svg>
					</li>';
					$chatroom .= '<li data-event="cr-head-tabs" title="'.$group_chat.'">
						<svg version="1.1" class="cr_svg_icon" id="cr_group_layer" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 width="24px" height="24px" viewBox="0 0 24 24" enable-background="new 0 0 24 24" xml:space="preserve">
						<g>
							<path fill="#757575" d="M12,14c1.381,0,2.631-0.561,3.536-1.465C16.439,11.631,17,10.381,17,9s-0.561-2.631-1.464-3.535
								C14.631,4.56,13.381,4,12,4S9.369,4.56,8.464,5.465C7.56,6.369,7,7.619,7,9s0.56,2.631,1.464,3.535C9.369,13.439,10.619,14,12,14z"
								/>
							<path fill="#757575" d="M20,15c0.689,0,1.314-0.279,1.768-0.73c0.453-0.453,0.732-1.078,0.732-1.77c0-0.69-0.279-1.315-0.732-1.768
								S20.689,10,20,10c-0.691,0-1.316,0.279-1.77,0.732c-0.451,0.453-0.73,1.078-0.73,1.768c0,0.691,0.279,1.316,0.73,1.77
								C18.684,14.723,19.309,15,20,15z"/>
							<path fill="#757575" d="M20,15.59c-1.331,0-2.332,0.406-2.917,0.969C15.968,15.641,14.205,15,12,15
								c-2.266,0-3.995,0.648-5.092,1.564C6.312,15.999,5.3,15.59,4,15.59c-2.188,0-3.5,1.09-3.5,2.182c0,0.545,1.312,1.093,3.5,1.093
								c0.604,0,1.146-0.052,1.623-0.134c-0.01,0.092-0.04,0.181-0.04,0.271c0,1,2.406,2,6.417,2c3.762,0,6.417-1,6.417-2
								c0-0.085-0.011-0.17-0.021-0.255c0.463,0.072,0.996,0.118,1.604,0.118c2.051,0,3.5-0.548,3.5-1.093
								C23.5,16.68,22.127,15.59,20,15.59z"/>
							<path fill="#757575" d="M4,15c0.69,0,1.315-0.279,1.768-0.732S6.5,13.189,6.5,12.5c0-0.689-0.279-1.314-0.732-1.768
								C5.315,10.28,4.69,10,4,10c-0.691,0-1.316,0.28-1.769,0.732C1.779,11.186,1.5,11.811,1.5,12.5c0,0.689,0.279,1.314,0.731,1.768
								C2.684,14.721,3.309,15,4,15z"/>
						</g>
						</svg>
					</li>';
					$chatroom .= '<li data-event="cr-head-tabs" title="'.$private_chat.'">
						<svg version="1.1" class="cr_svg_icon" id="cr_private_layer" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 width="24px" height="24px" viewBox="0 0 24 24" enable-background="new 0 0 24 24" xml:space="preserve">
<g>
	<path fill="#757575" d="M24,14.098c0-2.141-1.195-4.053-3.066-5.33c0.131,0.596,0.207,1.205,0.207,1.832
		c0,4.839-4.108,8.876-9.521,9.744c1.145,0.461,2.426,0.732,3.793,0.732c1.285,0,2.505-0.236,3.602-0.65
		c1.391,0.469,2.907,0.592,4.163,0.592c-0.655-0.778-1.104-1.551-1.431-2.225C23.143,17.555,24,15.908,24,14.098z"/>
	<path fill="#757575" d="M18.891,10.6c0-4.237-4.227-7.676-9.445-7.676C4.228,2.924,0,6.362,0,10.6c0,2.208,1.156,4.197,2.998,5.595
		c-0.346,0.896-0.932,2.063-1.926,3.259c1.887,0,4.315-0.246,6.188-1.396c0.704,0.137,1.433,0.213,2.187,0.213
		C14.663,18.271,18.891,14.836,18.891,10.6z"/>
</g>
</svg>
					</li>';
					$chatroom .= '<li data-event="cr-head-tabs" title="'.$profile.'">
						<svg version="1.1" class="cr_svg_icon" id="cr_home_layer" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
							 width="24px" height="24px" viewBox="0 0 24 24" enable-background="new 0 0 24 24" xml:space="preserve">
						<path fill="#757575" d="M13,3c0,0-6.186,5.34-9.643,8.232C3.154,11.416,3,11.684,3,12c0,0.553,0.447,1,1,1h2v7c0,0.553,0.447,1,1,1
							h3c0.553,0,1-0.448,1-1v-4h4v4c0,0.552,0.447,1,1,1h3c0.553,0,1-0.447,1-1v-7h2c0.553,0,1-0.447,1-1
							c0-0.316-0.154-0.584-0.383-0.768C19.184,8.34,13,3,13,3z"/>
						</svg>
					</li>';
					
				$chatroom .= '</ul>';
			$chatroom .= '</div>';
			$chatroom .= '<div class="chatroomBody">';
				$chatroom .= '<div class="cr-chat-box cr-visible">';
					$chatroom .= '<div class="cr-common-room">';
						$chatroom .= $this->commonroom_section_html();
					$chatroom .= '</div>';
				$chatroom .= '</div>';
				$chatroom .= '<div class="cr-chat-box">';
					$chatroom .= '<div class="cr-groups-room cr_relative cr_clear">';
						if(is_user_logged_in()){
							$chatroom .= $this->buddypress_section_html();
						}else{
							$chatroom .= '<div class="cr_center cr_bg"><b>'.$pls_login.'</b></div>';
						}
					$chatroom .= '</div>';
				$chatroom .= '</div>';
				$chatroom .= '<div class="cr-chat-box">';	
					$chatroom .= '<div class="cr-private-room cr_relative cr_clear">';
						if(is_user_logged_in()){
							$chatroom .= $this->private_section_html();
						}else{
							$chatroom .= '<div class="cr_center cr_bg"><b>'.$pls_login.'</b></div>';
						}
					$chatroom .= '</div>';
				$chatroom .= '</div>';
				$chatroom .= '<div class="cr-chat-box">';	
					$chatroom .= '<div class="cr-profile-home cr_relative cr_clear">';
						if(is_user_logged_in()){
							$chatroom .= $this->home_section_html();
						}else{
							$chatroom .= '<div class="cr_center cr_bg"><b>'.$pls_login.'</b></div>';
						}
					$chatroom .= '</div>';
				$chatroom .= '</div>';
			$chatroom .= '</div>';
			$chatroom .= '<form class="cr_file_upload" id="cr_chatroom_image_upload" method="post" enctype="multipart/form-data"  action="">
							  <input type="file" name="cr_chatroom_image_file" value="" id="cr_chatroom_image_file" />
							</form>';
			$chatroom .= '<form class="cr_video_upload" data-visibility="cr-element" id="cr_chatroom_video_upload" method="post" action="">
							  <input type="text" name="cr_chatroom_video_file" value="" placeholder="Youtube video id: 8MDHXfDlPhY" id="cr_chatroom_video_file" />
							  <input class="button button--ujarak" type="submit" id="cr_chatroom_video_submit" value="'.$submit.'" />
							</form>';
		$chatroom .= '</div>';
		
		return $chatroom;
	}
	
	public function commonroom_section_html(){
		
		global $wpdb;
		$htm = '';
		$html = '';
		
		$data = get_option('chatroom_options');
				
		$start_chat = !empty($data['cr_lg_chat'])?$data['cr_lg_chat']:'Start Chat';
		
		$p_login = !empty($data['cr_lg_plogin'])?$data['cr_lg_plogin']:'Login to chatroom';
		$p_signup = !empty($data['cr_lg_psignup'])?$data['cr_lg_psignup']:'Not a member? Please sign up.';
		
		$username = !empty($data['cr_lg_uname'])?$data['cr_lg_uname']:'Username';
		$fullname = !empty($data['cr_lg_fname'])?$data['cr_lg_fname']:'Full name';
		$email = !empty($data['cr_lg_email'])?$data['cr_lg_email']:'Email';
		$password = !empty($data['cr_lg_pass'])?$data['cr_lg_pass']:'Password';
		$cpassword = !empty($data['cr_lg_cpass'])?$data['cr_lg_cpass']:'Confirm password';
		
		$remember = !empty($data['cr_lg_mber'])?$data['cr_lg_mber']:'Remember me';
		
		$login = !empty($data['cr_lg_login'])?$data['cr_lg_login']:'Log in';
		$reg = !empty($data['cr_lg_reg'])?$data['cr_lg_reg']:'Register';
		
		$lostp = !empty($data['cr_lg_lostp'])?$data['cr_lg_lostp']:'Lost your password?';
		
		$newss = !empty($data['cr_lg_newss'])?$data['cr_lg_newss']:'New to chatroom?';
		
		$preg = !empty($data['cr_lg_preg'])?$data['cr_lg_preg']:'Please register';
		$areg = !empty($data['cr_lg_areg'])?$data['cr_lg_areg']:'Already member? Please login.';
		
		if(is_user_logged_in()){
			
			$UserId = get_current_user_id();
			$user = get_userdata( $UserId );
			$display_name = $user->display_name;
			
			$htm .= '<div class="cr_chat_wrap" data-userid="'.$UserId.'">
						<div id="cr_commonchat_body" class="cr_chat_body" data-userid="'.$UserId.'" data-location="commonroom-body-'.$UserId.'">
						<div class="cr_center cr_bg"><span>'.$common_room.'</span></div>
						</div>
						<div class="cr_chat_footer">
							<input type="text" data-event="submit-commonroom-msg" placeholder="'.$start_chat.'" data-window-id="'.$UserId.'" data-username="'.$display_name.'" data-userid="'.$UserId.'" />
							<span class="chatroomSmiley cr_smiley" data-event="smiley_open" data-type="chatroom" data-window-id="'.$UserId.'"></span>
							<span class="chatroomSmiley cr_image" data-event="cr_chatroom_image_open" data-type="chatroom" data-window-id="'.$UserId.'"></span>
							<span class="chatroomSmiley cr_video" data-event="cr_video_open" data-type="chatroom" data-window-id="'.$UserId.'"></span>
							<span class="chatroomSmiley cr_clip" data-event="cr_chatroom_file_open" data-type="chatroom" data-window-id="'.$UserId.'"></span>
						</div>
					</div>';
		}else{
			$htm .= '<div class="cr_login_wrap">
					<div id="cr_login_msg"></div>
					<div class="cr_center cr_pad"><b>'.$p_login.'</b></div>
					<form action="'. wp_login_url(home_url()).'" id="cr_login_form" name="login_form" method="post">
						  <div>
							<!--[if IE ]>
							   <span>'.$username.'</span><br/>
							<![endif]-->
							<input type="text" id="username" required name="log" class="inputbox" value="" placeholder="'.$username.'">
						  </div>
						  <div>
							<!--[if IE ]>
							   <span>'.$password.'</span><br/>
							<![endif]-->
							<input type="password" id="password" value="" required name="pwd" class="inputbox" placeholder="'.$password.'">
						  </div>
						  <div class="cr_clear">
						  	<div class="cr_forgetmenot">
								<label for="rememberme"><input name="rememberme" type="checkbox" id="rememberme" value="forever">'.$remember.'</label>
								<label for="forgetpass"><a href="'.wp_lostpassword_url().'" title="Password Lost and Found">'.$lostp.'</a></label>
						  	</div>
							<div class="cr_login_submit">
								<button class="button button--ujarak" type="submit" id="cr_login_submit" name="wp-submit">'.$login.'</button>
							</div>
						  </div>
					</form>
				</div>
				<div class="cr_logres_wrap">
					<div>
						<div class="cr_center cr_pad"><b class="open-register">'.$newss.'</b></div>
						<button class="open-register button button--ujarak" data-event="open-chatroom-register">'.$preg.'</button>
					</div>
					<div>
						<div class="cr_center cr_pad"><b class="open-log-in">'.$areg.'</b></div>
						<button class="open-log-in button button--ujarak" data-event="open-chatroom-login">'.$login.'</button>
					</div>
				</div>
				<div class="cr_register_wrap">
					<div class="cr_center cr_pad"><b>'.$p_signup.'</b></div>
					<form id="cr_register_form" action="" method="post">
					  <div>
					  	<!--[if IE ]>
						   <span>'.$username.'</span><br/>
						<![endif]-->
						<input type="text" id="cr_signup_username" name="cr_signup_username" class="inputbox" required placeholder="'.$username.'">
					  </div>
					  <div>
					  	<!--[if IE ]>
						   <span>'.$fullname.'</span><br/>
						<![endif]-->
						<input type="text" id="cr_signup_fullname" name="cr_signup_fullname" class="inputbox" required placeholder="'.$fullname.'">
					  </div>
					  <div>
					  	<!--[if IE ]>
						   <span>'.$email.'</span><br/>
						<![endif]-->
						<input type="text" id="cr_signup_email" name="cr_signup_email" class="inputbox" required placeholder="'.$email.'">
					  </div>
					  <div>
					  	<!--[if IE ]>
						   <span>'.$password.'</span><br/>
						<![endif]-->
						<input type="password" id="cr_signup_password" name="cr_signup_password" class="inputbox" required placeholder="'.$password.'">
					  </div>
					  <div>
					  	<!--[if IE ]>
						   <span>'.$cpassword.'</span><br/>
						<![endif]-->
						<input type="password" id="cr_signup_password_confirm" name="cr_signup_password_confirm" class="inputbox" required placeholder="'.$cpassword.'">
					  </div>
					  <div>
						<button type="submit" data-event="chatroom-register-user" id="cr_signup_submit" name="cr_signup_submit" class="button button--ujarak">'.$reg.'</button> 
					  </div>
					  <div id="cr_register_msg"></div>
					</form>
				</div>';
		}

		$html .= '<div class="cr_commonroomBody">
						'.$htm.'
				</div>';
		
		return $html;
	}

	public function private_section_html(){
		
		global $wpdb;
		$htm = '';
		$html = '';
		
		$data = get_option('chatroom_options');
		$search_friends = !empty($data['cr_lg_search_friends'])?$data['cr_lg_search_friends']:'Search...';
		$refresh = !empty($data['cr_lg_refresh'])?$data['bpc_lg_refresh']:'Refresh';
		$monline = !empty($data['cr_lg_monline'])?$data['bpc_lg_monline']:'Members online';
		$glist = !empty($data['cr_lg_glist'])?$data['bpc_lg_glist']:'Group List';
		
		$gsql = $wpdb->get_results( "SELECT id FROM {$wpdb->prefix}bp_groups ORDER BY id LIMIT 1");
		
		$UserId = get_current_user_id();
		
		$htm .= '<div class="cr_col cr_twothird">';	
			$htm .= '<div id="cr_private_chat_wrap" class="cr_chat_wrap cr_relative">
						<ul id="cr_private_chat_tab" class="cr-left-chat-tab"></ul>
						<div id="cr_private_chat_box"></div>
					</div>';
		$htm .= '</div>';
		$htm .= '<div class="cr_col cr_onethird">';	
				$htm .= '<div class="cr_relative">';
					$htm .= '<span class="cr_res_tab" data-visibility="cr-close" data-tab="cr-private" data-event="cr-open-res-tab"></span>';
					$htm .= '<div id="cr-private-tab">';
						$htm .= '<div class="cr-tab-content">';
							$htm .= '<div id="cr-private-search">';
							$htm .= '<input type="text" id="chatroomSearchFriends" placeholder="'.$search_friends.'" />';
							$htm .= '</div>';
							$htm .= '<div class="chatroomFriendsFilter">';
										$htm .= '<span class="RefreshMembersList" title="'.$refresh.'" data-event="cr_refresh_friends"><img src="'.MCR_URL.'images/refresh.png"/></span>';
										$htm .= '<span class="LoadMembersOnline" title="'.$monline.'" data-event="cr_online_friends"><img src="'.MCR_URL.'images/circle.png"/></span>';
										
										if(!empty($gsql)){
										$htm .= '<span class="LoadGroupList" title="'.$glist.'" data-event="cr_bp_group_list"><img src="'.MCR_URL.'images/friends.png"/></span>';
										}
			   				$htm .= '</div>';
							$htm .= '<div id="cr-private-userlist">';
							$htm .= $this->cr_private_userlist_function();
							$htm .= '</div>';
						$htm .= '</div>';
					$htm .= '</div>';
				$htm .= '</div>';
		$htm .= '</div>';
		return $htm;

	}
	
	public function buddypress_section_html(){
		
		global $wpdb;
		$htm = '';
		$html = '';
		
		$data = get_option('chatroom_options');
		$group_room = !empty($data['cr_lg_group_room'])?$data['cr_lg_group_room']:'Group chat room';
		
		$UserId = get_current_user_id();
		
		$htm .= '<div class="cr_col cr_twothird">';	
			$htm .= '<div id="cr_group_chat_wrap" class="cr_chat_wrap cr_relative">
						<ul id="cr_group_chat_tab" class="cr-left-chat-tab"></ul>
						<div id="cr_group_chat_box"></div>
					</div>';
		$htm .= '</div>';
		$htm .= '<div class="cr_col cr_onethird">';	
				$htm .= '<div class="cr_relative">';
					$htm .= '<span class="cr_res_tab" data-visibility="cr-close" data-tab="cr-buddypress" data-event="cr-open-res-tab"></span>';
					$htm .= '<div id="cr-buddypress-tab">';
						$htm .= '<div class="cr-tab-content">';
							$htm .= '<div class="cr_center cr_wbg"><span>'.$group_room.'</span></div>';
							$htm .= '<div id="cr-buddypress-grouplist">';
							$htm .= $this->cr_bp_group_member_loop();
							$htm .= '</div>';
						$htm .= '</div>';
					$htm .= '</div>';
				$htm .= '</div>';
		$htm .= '</div>';
		
		return $htm;

	}
	
	public function home_section_html(){
		
		global $wpdb;
		$htm = '';
		$html = '';
		
		$data = get_option('chatroom_options');
		
		$group = !empty($data['cr_lg_cgroup'])?$data['cr_lg_cgroup']:'Create Group';
		
		$UserId = get_current_user_id();
		
		$user = get_userdata($UserId);
		$name = $user->display_name?$user->display_name:$user->user_login;
		$email = $user->user_email;
		
		$image_src = wp_get_attachment_image_src( get_usermeta($UserId,'cr_thumbnail_id',true )); 
		
		if($image_src[0]){
			$user_image = '<img id="cr_profile_image_'.$UserId.'" data-event="edit-cr-profile-image" class="cr_profile_image" src="'.$image_src[0].'">';
		}else{
			$getAvater = str_replace('&','&amp;',get_avatar($UserId));
			$doc = new DOMDocument();
			$doc->loadHTML($getAvater);
			$xpath = new DOMXPath($doc);
			
			$src = $xpath->evaluate("string(//img/@src)"); # "/images/image.jpg"
			
			$user_image = '<img id="cr_profile_image_'.$UserId.'" data-event="edit-cr-profile-image" class="cr_profile_image" src="'.$src.'">';
		}
				
		
		$htm .= '<div class="cr_col cr_half">';	
			$htm .= '<div class="cr_homeleft">';
				$htm .= '<div class="cr_user_info cr_clear">';
				$htm .= $user_image;
				$htm .='<b>'.$name.'</b><br/>';
				$htm .='<span>'.$email.'</span><br/>';
				$htm .='<a href="'.get_edit_user_link($UserId).'" title="Edit"><img src="'.MCR_URL.'images/edit.png"></a>';
				$htm .= '</div>';
				$htm .= '<h3>'.$group.'</h3>';
				$htm .= '<div class="cr_bp_user_group cr_clear">';
				$htm .= $this->cr_create_bp_group();
				$htm .= '</div>';
				
			$htm .= '</div>';
		$htm .= '</div>';
		$htm .= '<div class="cr_col cr_half">';	
			$htm .= '<div class="cr_homeright">';
			$htm .= $this->cr_bp_group_loop();
			$htm .= '</div>';
		$htm .= '</div>';
		
		return $htm;
	}
	
	public function cr_create_bp_group(){
		
		$htm = '';
		$data = get_option('chatroom_options');
		
		$c_group = !empty($data['cr_lg_create'])?$data['cr_lg_cgroup']:'Create';
		$g_name = !empty($data['cr_lg_g_name'])?$data['cr_lg_g_name']:'Group Name';
		$g_desc = !empty($data['cr_lg_g_desc'])?$data['cr_lg_g_desc']:'Group Description';
		$pu_group = !empty($data['cr_lg_pu_group'])?$data['cr_lg_pu_group']:'Public group';
		$pv_group = !empty($data['cr_lg_pv_group'])?$data['cr_lg_pv_group']:'Private group';
		$ph_group = !empty($data['cr_lg_ph_group'])?$data['cr_lg_ph_group']:'Hidden group';
		$am_group = !empty($data['cr_lg_am_group'])?$data['cr_lg_am_group']:'All members';
		$amo_group = !empty($data['cr_lg_amo_group'])?$data['cr_lg_amo_group']:'Admins and mods only';
		$ao_group = !empty($data['cr_lg_ao_group'])?$data['cr_lg_ao_group']:'Admins only';
		
		
		$htm .= '<form action="" method="post" id="cr-create-group-form" enctype="multipart/form-data">
		
			<div id="cr-group-create-body">
				<div>
					<!--[if IE ]><label for="group-name">'.$g_name.'</label><![endif]-->
					<input name="group-name" placeholder="'.$g_name.'" id="cr-group-name" aria-required="true" value="" type="text">
				</div>
	
				<div>
					<!--[if IE ]><label for="group-desc">'.$g_desc.'</label><![endif]-->
					<textarea name="group-desc" placeholder="'.$g_desc.'" id="cr-group-desc" aria-required="true"></textarea>
				</div>
				<div class="cr_clear cr-radio-wrap">
					<div>Privacy Settings</div>
					<div class="cr-radio">
						<div>
							<input name="cr-group-status" value="public" checked="checked" type="radio"> 
							<span>'.$pu_group.'</span>
						</div>
						<div>
							<input name="cr-group-status" value="private" type="radio">
							<span>'.$pv_group.'</span>
						</div>
						<div>
							<input name="cr-group-status" value="hidden" type="radio">
							<span>'.$ph_group.'</span>
						</div>
					</div>
					<div class="cr-radio">
						<div>
							<input name="cr-group-invite-status" value="members" checked="checked" type="radio">
							<span>'.$am_group.'</span>
						</div>
	
						<div>
							<input name="cr-group-invite-status" value="mods" type="radio">
							<span>'.$amo_group.'</span>
						</div>
	
						<div>
							<input name="cr-group-invite-status" value="admins" type="radio">
							<span>'.$ao_group.'</span>
						</div>
					</div>
				</div>
				<div id="cr-creat-group-btn">
					<button data-event="cr-create-group" data-userid="'.$this->user_id.'" id="cr-group-creation-create" name="cr-save-group" class="button button--ujarak">'.$c_group.'</button> 
				</div>
			</div>
		</form>';
		
		return $htm;
		
	}
	
	public function cr_bp_group_loop(){
		
		$bgroup ='';
		if ( bp_has_groups() ) : 
		  	$bgroup .= '<h3>Group list</h3>';
			$bgroup .= '<ul class="cr_groups_list">';
			while ( bp_groups() ) : bp_the_group();
			
				$string = stripslashes(htmlspecialchars(bp_get_group_name()));
				$group_name = (mb_strlen($string) > 20) ? mb_substr($string,0,17).'...' : $string;
				
				$split = explode(" ",bp_get_group_member_count());
				$member_no = $split[0];
		 
				$bgroup .= '<li class="cr_clear"> ';
						$bgroup .= '<div class="cr_group_title"><a href="'.bp_get_group_permalink().'">'. $group_name .'</a> ('.$member_no.')</div>';
					$bgroup .= '<div class="cr_group_button action">';
						$bgroup .= bp_get_group_join_button();
						//$bgroup .= '<div class="meta">';
							//$bgroup .= bp_get_group_type() . ' / ' . bp_get_group_member_count();
						//$bgroup .= '</div>';
					$bgroup .= '</div>';
				$bgroup .= '</li>';
		 
			endwhile;
			$bgroup .= '</ul>';
			$bgroup .= '<div class="pagination-links" id="group-dir-pag">';
				$bgroup .= bp_get_groups_pagination_links();
		   $bgroup .= '</div>';
		 endif; 
		 
		 return $bgroup;
	}
	
	public function cr_bp_group_member_loop(){
		
		$user_id = get_current_user_id();
		$args = array(
			 'user_id' => $user_id
		);
		$bgroup ='';
		if ( bp_has_groups($args) ): 
			$bgroup .= '<ul class="cr_mgroups_list">';
			while ( bp_groups() ) : bp_the_group();
			
				$string = stripslashes(htmlspecialchars(bp_get_group_name()));
				$group_name = (mb_strlen($string) > 16) ? mb_substr($string,0,13).'...' : $string;
				
				$split = explode(" ",bp_get_group_member_count());
				$member_no = $split[0];
		 
				$bgroup .= '<li class="cr_clear"> ';
						$bgroup .= '<div class="cr_mgroup_title" data-groupid="'.bp_get_group_id().'" data-event="cr-open-group-chat">'. $group_name .' ('.$member_no.')</div>';
						$bgroup .= '<div class="cr_mgroup_nav" data-groupid="'.bp_get_group_id().'" data-event="cr-open-member-list"><span>&or;</span></div>';
						$bgroup .= '<div id="mgroup_id_'.bp_get_group_id().'" class="cr_mgroup_name cr_clear" data-mlist-state="0">';
							if ( bp_group_has_members( 'group_id='.bp_get_group_id()) ) :
								while ( bp_group_members() ) : bp_group_the_member();
								
								if($this->chat_user_online(bp_get_group_member_id())){
									$bgroup .= '<i class="groupChatStatus chat_online_circle"></i>';
								}else{
									$bgroup .= '<i class="groupChatStatus chat_offline_circle"></i>';
								}
								
								$name = stripslashes(htmlspecialchars(bp_get_group_member_name()));
								$member_name = (mb_strlen($name) > 16) ? mb_substr($name,0,13).'...' : $name;
								$bgroup .= '<span>'.$member_name.'</span>';
								
								endwhile;
							endif;
						$bgroup .= '</div>';
				$bgroup .= '</li>';
		 
			endwhile;
			$bgroup .= '</ul>';
			$bgroup .= '<div class="pagination-links" id="group-dir-pag">';
				$bgroup .= bp_get_groups_pagination_links();
		   $bgroup .= '</div>';
		 endif; 
		 
		 return $bgroup;
	}
		
	/*
     * load_friend functions for handling AJAX request
     *
     * @param  -
     * @return -
     */

    public function cr_private_userlist_function() {
		
		$FriendsRow = '';
		$data = get_option('chatroom_options');
    	$no_result = !empty($data['cr_lg_no_result'])?$data['cr_lg_no_result']:'No results.';
		
		global $wpdb;
		$wpdb->show_errors = true;
				
		$UserId = get_current_user_id();
		
		$FriendsSQL = $wpdb->get_results($wpdb->prepare("SELECT id, display_name FROM $wpdb->users WHERE id NOT LIKE '%d' ORDER BY id LIMIT 50", $UserId));		

		if(!empty($FriendsSQL)){
			foreach($FriendsSQL as $Row) {
				$ID = $Row->id;
				$string = stripslashes(htmlspecialchars($Row->display_name));
				$DisplayName = (mb_strlen($string) > 17) ? mb_substr($string,0,15).'..' : $string;
				$getAvater = str_replace('&','&amp;',get_avatar($Row->id));
				$doc = new DOMDocument();
				$doc->loadHTML($getAvater);
				$xpath = new DOMXPath($doc);
				$src = $xpath->evaluate("string(//img/@src)"); # "/images/image.jpg"
				
				$online = $this->chat_user_online($ID)==true?'chat_online':'chat_offline';
				
				$FriendsRow .= '<div data-event="cr-private-chat-init" data-cr-username="'.$DisplayName.'" data-cr-userid="'.$ID.'" class="chatroomFriendsRow cr_clear"><i class="chatStatus '.$online.'_circle"></i><img class="chatroomFriendsImage '.$online.'" src="'.$src.'" /><div class="chatroomFriendsName">'.$DisplayName.'</div></div>';
			}
		}else{
			
			$FriendsRow .= "<center style=\"margin: 10px\">".$no_result."</center>";
		}
		
		return $FriendsRow;					
    }

}


?>
