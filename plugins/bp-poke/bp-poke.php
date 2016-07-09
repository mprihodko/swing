<?php

/**
 * Plugin Name: (BuddyDev) BP Poke
 * Plugin URI: http://buddydev.com/plugins/bp-poke/
 * Version: 1.0.6
 * Author: Anu Sharma
 * Author URI: http://buddydev.com/members/anusharma/
 * Description: Allow Users to poke each other
 */

if ( ! defined( 'ABSPATH' ) ) exit;

//define constants
//bp poke plugin dir url
define( 'BP_POKE_DIR_URL', plugin_dir_url( __FILE__ ) );

//plugin dir path with trailing slash
define( 'BP_POKE_DIR_PATH', plugin_dir_path( __FILE__ ) );



class BP_Poke_Helper {
    
    private static $instance;
    
    private function __construct() {
        
        add_action( 'bp_loaded',         array( $this, 'load_files' ), 0 );
       // add_action( 'bp_enqueue_scripts', array( $this, 'load_js' ) );
         //load text domain
        add_action ( 'bp_loaded',         array( $this, 'load_textdomain' ), 2 );
    }
    
    public static function get_instance() {
        
        if( ! isset( self::$instance ) )
            self::$instance = new self();
        
        return self::$instance;
        
    }
     /**
     * Load plugin textdomain for translation
     */
    public function load_textdomain(){
        
         $locale = apply_filters( 'bp-poke_get_locale', get_locale() );
        
        // if load .mo file
        if ( ! empty( $locale ) ) {
            $mofile_default = sprintf( '%slanguages/%s.mo', plugin_dir_path(__FILE__), $locale );
            $mofile = apply_filters( 'bp-poke_load_textdomain_mofile', $mofile_default );

			if (is_readable( $mofile ) ) 
				// make sure file exists, and load it
				load_textdomain( 'bp-poke', $mofile );
        }
       
    }
    
    public function load_files() {
 
        $files = array(
			'bp-poke-functions.php',
			'bp-poke-component.php',
			'bp-poke-actions.php',        
			'bp-poke-screens.php'
        );
        
        foreach( $files as $file )
            require_once BP_POKE_DIR_PATH . $file ;
    }

}

BP_Poke_Helper::get_instance();

add_action( 'bp_setup_components', 'bp_poke_setup_comonent', 6 );

function bp_poke_setup_comonent() {
    
	$bp = buddypress();
        
    $bp->poke = new BP_Poke_Component();
    
}