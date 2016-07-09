<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

class BP_Poke_Component extends BP_Component {

	/**
	 * Start the groups component creation process
	 *
	 * 
	 */
	public function __construct() {
           
            global $bp;
            parent::start(
                    'poke',
                    __( 'Poke', 'bp-poke' ),
                    BP_POKE_DIR_URL
            );
                
            $bp->active_components[$this->id] = 1;
            
            //add_action( 'bp_setup_nav', array( $this, 'setup_settings_nav' ), 11 );
	}

	/**
	 * Include files
	 */
	public function includes( $files = array() ) {

	}

	/**
	 * Setup globals
	 *
	 */
	public function setup_globals( $globals = array() ) {
		global $bp;

		if( ! defined( 'BP_POKE_SLUG' ) )
			define( 'BP_POKE_SLUG', 'pokes' );

		// Note that global_tables is included in this array.
		$globals = array(
			'slug'                  => BP_POKE_SLUG,
			'has_directory'         => false,
			'notification_callback' => 'bp_poke_format_notifications',
			'global_tables'         => false
		);

		parent::setup_globals( $globals );

	}
      
     
}

