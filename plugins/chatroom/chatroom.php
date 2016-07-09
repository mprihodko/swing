<?php

/*		
	Plugin Name: chatroom
	Plugin URI: http://chatroom.mircode.com/
	Description: chatroom is a wordpress chat room and group chat plugin. It also support buddypress group chat. 
	Version: 1.0.2
	Author: Mircode Solution
	Author URI: http://codecanyon.net/user/mircode
	License: GPLv2
*/
	
if( !defined('MCR_PATH') )
	define( 'MCR_PATH', plugin_dir_path(__FILE__));
if( !defined('MCR_URL') )
	define( 'MCR_URL', plugin_dir_url(__FILE__ ));
	
	
//with trailing slash
if( !defined('MC_TEXT_DOMAIN') )
	define( 'MC_TEXT_DOMAIN', 'chatroom' );
	
add_action( 'wp_footer', 'cr_sound_function');
require_once 'ajax/class-chatroom-ajax.php';
require_once 'controllers/class-chatroom-database-controller.php';
require_once 'admin/class-chatroom-options.php';

class chatroom_Apps {
	
	 public function __construct() {
		 
    }
	
    public function initialize_controllers() {

        require_once 'controllers/class-chatroom-activation-controller.php';
        $activation_controller = new Chatroom_Activation_Controller();
        $activation_controller->initialize_activation_hooks();
		
		require_once 'controllers/class-chatroom-schedule-controller.php';
        $schedule_controller = new Chatroom_Schedule_Controller();
		
		require_once 'controllers/class-chatroom-shortcode-controller.php';
        $shortcode_controller = new Chatroom_Shortcode_Controller();
		
		require_once 'controllers/class-chatroom-script-controller.php';
        $script_controller = new Chatroom_Script_Controller();
        $script_controller->enque_scripts();

    }

    public function initialize_app_controllers() {

        $ajax = new chatroom_Ajax();
        $ajax->initialize();

    }
	

}

$chatroom_app = new chatroom_Apps();
$chatroom_app->initialize_controllers();

function load_chatroom(){
	
	$chatroom_init = new chatroom_Apps();
	$chatroom_init->initialize_app_controllers();
	
	//$test = new chatroom_Admin_Ajax();
	//var_dump ($test->isp_add_search_content_function());
	//$test1 = new chatroom_Ajax();
	//var_dump ($test1->cr_get_private_chat_row_function());
	//$data = get_option('chatroom_options');
	//var_dump( $data['isp_bg_image']['src']);
	//exit;
}

add_action('init', 'load_chatroom');

function cr_sound_function() {
	$sound = '';
	$sound .= '<audio id="chatroom_alert">';
	$sound .= '<source src="' . plugins_url() . '/chatroom/images/alert.ogg" type="audio/ogg">';
	$sound .= '<source src="' . plugins_url() . '/chatroom/images/alert.mp3" type="audio/mpeg">';
	$sound .= '</audio>';
	echo $sound;
}
  
  

?>