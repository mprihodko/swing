<?php

class Chatroom_Script_Controller{

    public function enque_scripts(){
        add_action('wp_enqueue_scripts', array($this, 'include_scripts_styles'));
		add_action('wp_head', array($this, 'include_custom_styles'));
        add_action('admin_enqueue_scripts', array($this, 'include_admin_scripts_styles'));
    }

    /*
     * Include AJAX plugin specific scripts and pass the neccessary data.
     *
     * @param  -
     * @return -
     */

    public function include_scripts_styles(){
        global $post;
		
		$data = get_option('chatroom_options');
    	$chatRate = $data['chat_refresh_rate'];
		
        wp_register_script('chatroom_ajax', plugins_url('js/chatroom-ajax.js', dirname(__FILE__)), array("jquery"));
        wp_enqueue_script('chatroom_ajax');

        $nonce = wp_create_nonce("unique_key");

        $ajax = new chatroom_AJAX();
        $ajax->initialize();
		
        $getAvater = str_replace('&','&amp;',get_avatar(get_current_user_id()));
		$doc = new DOMDocument();
		$doc->loadHTML($getAvater);
		$xpath = new DOMXPath($doc);
		$src = $xpath->evaluate("string(//img/@src)");
				
        $config_array = array(
            'ajaxURL' => admin_url('admin-ajax.php'),
            'ajaxActions' => $ajax->ajax_actions,
            'ajaxNonce' => $nonce,
            'siteURL' => site_url(),
			'pluginsURL' => plugins_url(),
			'templateURL' => plugins_url('template/', dirname(__FILE__)),
			'chatRate' => $chatRate,
			'avatar' => $src,
        );

        wp_localize_script('chatroom_ajax', 'chatroom_conf', $config_array);

        wp_register_style('chatroom_styles', plugins_url('css/chatroom-style.css', dirname(__FILE__)));
        wp_enqueue_style('chatroom_styles');

    }

    public function colourchanger($hex, $percent) {
		// Work out if hash given
		$hash = '';
		if (stristr($hex,'#')) {
			$hex = str_replace('#','',$hex);
			$hash = '#';
		}
		/// HEX TO RGB
		$rgb = array(hexdec(substr($hex,0,2)), hexdec(substr($hex,2,2)), hexdec(substr($hex,4,2)));
		for ($i=0; $i<3; $i++) {
			// See if brighter or darker
			if ($percent > 0) {
				// Lighter
				$rgb[$i] = round($rgb[$i] * $percent) + round(255 * (1-$percent));
			} else {
				// Darker
				$positivePercent = $percent - ($percent*2);
				$rgb[$i] = round($rgb[$i] * $positivePercent) + round(0 * (1-$positivePercent));
			}
			// In case rounding up causes us to go to 256
			if ($rgb[$i] > 255) {
				$rgb[$i] = 255;
			}
		}
		//// RBG to Hex
		$hex = '';
		for($i=0; $i < 3; $i++) {
			// Convert the decimal digit to hex
			$hexDigit = dechex($rgb[$i]);
			// Add a leading zero if necessary
			if(strlen($hexDigit) == 1) {
			$hexDigit = "0" . $hexDigit;
			}
			// Append to the hex string
			$hex .= $hexDigit;
		}
		return $hash.$hex;
	}
	//$colour = '#ae64fe';
	//$brightness = 0.5; // lighter
	//$brightness = 0.3; // more lighter
	//$brightness = 0.1; // close to white
	//$newColour = colourchanger($colour,$brightness);
	//$colour = '#ae64fe';
	//$brightness = -0.5; // 50% darker
	//$brightness = -0.3; // more darker
	//$brightness = -0.1; // more darker close to black
	//$newColour = colourchanger($colour,$brightness);
	public function hex2rgba($hex,$opc) {
	   $hex = str_replace("#", "", $hex);
	
	   if(strlen($hex) == 3) {
		  $r = hexdec(substr($hex,0,1).substr($hex,0,1));
		  $g = hexdec(substr($hex,1,1).substr($hex,1,1));
		  $b = hexdec(substr($hex,2,1).substr($hex,2,1));
	   } else {
		  $r = hexdec(substr($hex,0,2));
		  $g = hexdec(substr($hex,2,2));
		  $b = hexdec(substr($hex,4,2));
	   }
	   $rgb = 'rgba('.$r.','.$g.','.$b.','.$opc.')';
	   //return implode(",", $rgb); // returns the rgb values separated by commas
	   return $rgb; // returns an array with the rgb values
	}
	
	public function include_custom_styles(){
		
		$data = get_option('chatroom_options');
    	$header_bgcolor = !empty($data['cr_title_bg_color']) && $data['cr_title_bg_color'] != '#'?$data['cr_title_bg_color']:'#eaeaea';
		$header_bdrcolor = $this->colourchanger($header_bgcolor, -.6);
		$header_iconcolor = !empty($data['cr_title_color']) && $data['cr_title_color']!='#'?$data['cr_title_color']:'#757575';
		$header_iconhover = !empty($data['cr_title_color_hover']) && $data['cr_title_color_hover']!='#'?$data['cr_title_color_hover']:'#cb0e40';
				
		$chatroomBodybgc = !empty($data['cr_chat_bg_color']) && $data['cr_chat_bg_color']!='#'?$data['cr_chat_bg_color']:'';
		$chatroomBodybgp = $data['cr_chat_bg_pattern']!='none'? 'url('.MCR_URL . 'images/bg/'.$data['cr_chat_bg_pattern'].'.png) repeat;':'';
		$chatroomBodybgi = !empty($data['cr_chat_bg_image'])? 'url('.$data['cr_chat_bg_image']['src'].') no-repeat center center;':'';
		
		if($chatroomBodybgi){
			$chatroomBodybg = $chatroomBodybgi;
		}else if($chatroomBodybgp){
			$chatroomBodybg = $chatroomBodybgp; 
		}else if($chatroomBodybgc){
			$chatroomBodybg = $chatroomBodybgc;
		}
		$chatroomStartChatBdr = !empty($data['cr_start_chat_bdr_color']) && $data['cr_start_chat_bdr_color'] != '#'?'border-top-color:' .$data['cr_start_chat_bdr_color'].';':'';
		$chatroomStartChatBg = !empty($data['cr_start_chat_bg_color']) && $data['cr_start_chat_bg_color'] != '#'?'background-color:' .$data['cr_start_chat_bg_color'].';':'';
		$cr_placeholder_text = !empty($data['cr_placeholder_text_color']) && $data['cr_placeholder_text_color'] != '#'?'
		.cr_chat_body input::-webkit-input-placeholder{
		   color: '.$data['cr_placeholder_text_color'].';
		}
		.cr_chat_body input:-moz-placeholder{
		   color: '.$data['cr_placeholder_text_color'].';  
		}
		.cr_chat_body input::-moz-placeholder{
		   color: '.$data['cr_placeholder_text_color'].';  
		}
		.cr_chat_body input:-ms-input-placeholder{  
		   color: '.$data['cr_placeholder_text_color'].';  
		}
		':'';
		$chatroomColor = !empty($data['cr_chat_text_color']) && $data['cr_chat_text_color']!='#'?$data['cr_chat_text_color']:'#555555';

		$chatroomBdrBottom = !empty($data['cr_chatbox_bg_color']) && $data['cr_chatbox_bg_color']!='#'?'border-bottom-color:' .$this->colourchanger($data['cr_chatbox_bg_color'], -.6).';':'';
		
		if($data['enable_cr_chatbox_bg_opacity'] && !empty($data['cr_chatbox_bg_color']) && $data['cr_chatbox_bg_color'] != '#'){
			
			$chatroomBgc = 'background-color:' .$this->hex2rgba($data['cr_chatbox_bg_color'], $data['cr_chatbox_bg_opacity']).';';
			
			$chatroomTip ='
			.leftMessage:after{border-color:rgba(255, 255, 255,0);border-right-color:'.$this->hex2rgba($data['cr_chatbox_bg_color'],$data['cr_chatbox_bg_opacity']-.2).';}
			.leftMessage:before{border-color:rgba(218, 222, 225,0);border-right-color:'.$this->hex2rgba($data['cr_chatbox_bg_color'],$data['cr_chatbox_bg_opacity']-.2).';}
			.rightMessage:after{border-color:rgba(255, 255, 255,0);border-left-color:'.$this->hex2rgba($data['cr_chatbox_bg_color'],$data['cr_chatbox_bg_opacity']-.2).';}
			.rightMessage:before{border-color:rgba(218, 222, 225,0);border-left-color:'.$this->hex2rgba($data['cr_chatbox_bg_color'],$data['cr_chatbox_bg_opacity']-.2).';}
			';
		}else if(!empty($data['cr_chat_bg_color']) && $data['cr_chat_bg_color'] != '#'){
			$chatroomBgc = 'background-color:' .$data['cr_chatbox_bg_color'].';';
			
			$chatroomTip = '
			.leftMessage:after{border-color: rgba(255, 255, 255, 0);border-right-color: '.$data['cr_chatbox_bg_color'].';}
			.leftMessage:before{border-color: rgba(218, 222, 225, 0);border-right-color: '.$data['cr_chatbox_bg_color'].';}
			.rightMessage:after{border-color: rgba(255, 255, 255, 0);border-left-color: '.$data['cr_chatbox_bg_color'].';}
			.rightMessage:before{border-color: rgba(218, 222, 225, 0);border-left-color: '.$data['cr_chatbox_bg_color'].';}
			';
		}
				
		echo "<style type=\"text/css\">				
		
		.chatroomWindow .chatroomHeader{background-color: ".$header_bgcolor."; border-bottom: 1px solid ".$header_bdrcolor." } 
		.chatroomWindow ul.cr-head-tabs li .cr_svg_icon path{
			fill: ".$header_iconcolor.";
		}
		.chatroomWindow ul.cr-head-tabs li.cr-current .cr_svg_icon path,
		.chatroomWindow ul.cr-head-tabs li:hover .cr_svg_icon path{
			fill: ".$header_iconhover.";
		}
		
		.chatroomBody{background: ".$chatroomBodybg." } 
		
		.chatroomMessage{".$chatroomBgc.$chatroomBdrBottom."}
		.cr_chat_body input[type=\"text\"]{".$chatroomStartChatBg.$chatroomStartChatBdr."} 
		.chatroomContent{color: ".$chatroomColor."}".$cr_placeholder_text.$chatroomTip."</style>"; 
    }
	
    public function include_admin_scripts_styles(){

        wp_register_style( 'chatroom_admin_css', plugins_url('css/chatroom-admin.css', dirname(__FILE__)));
        wp_enqueue_style( 'chatroom_admin_css' );
    }


}

?>