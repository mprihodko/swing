<?php

class Chatroom_Activation_Controller {

    public function initialize_activation_hooks() {
        register_activation_hook("chatroom/chatroom.php", array($this, 'execute_activation_hooks'));
		//register_deactivation_hook(__FILE__, array($this, 'execute_deactivation_hooks'));
		//register_uninstall_hook(__FILE__, array($this, 'execute_uninstall_hooks'));
    }

    public function execute_activation_hooks() {
		
        $database_manager = new chatroom_Database_Manager();
		
        $database_manager->create_custom_tables();
		        
    }
	
	public function execute_deactivation_hooks() {
		// Will be executed when the client deactivates the plugin
    }
	public function execute_uninstall_hooks() {
		// Will be executed when the client deactivates the plugin
		
    }


}

?>