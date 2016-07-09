<?php

if ( !defined( 'ABSPATH' ) ) exit;

function bp_poke_can_user_poke( $from, $to ) {
    
    $pokes = bp_get_user_meta( $to, 'pokes', true );
    
    if( isset( $pokes[$from] ) )
        return false;
	
    return true;
}

function bp_poke_can_user_poke_back( $from, $to ) {
    
    $pokes = bp_get_user_meta( $from, 'pokes', true );
    
    if( isset( $pokes[$to] ) )
        return true;
    return false;
}

function bp_poke_user_did_poke( $user_id ) {
    
    $other_user_id = get_current_user_id();
    return bp_poke_can_user_poke_back( $other_user_id, $user_id );
    
}

function bp_poke_get_poke_list_url( $user_id = false ) {
    if( ! $user_id )
        $user_id =  get_current_user_id ();
    
    return bp_core_get_user_domain( $user_id ) . bp_get_activity_slug() . '/' . BP_POKE_SLUG . '/';
}

function bp_poke_get_poke_back_url( $user_id ) {
    
    return bp_poke_get_poke_url( $user_id, 'poke_back' );
}
function bp_poke_get_poke_url( $user_id, $action ) {
    
    $url = bp_poke_get_poke_list_url().'?poke_action=' . $action. '&user_id='. $user_id. '&_wpnonce=' . wp_create_nonce( 'poke_action' );
    
    return $url;
}
//show notifications to user.

function bp_poke_format_notifications( $action , $item_id, $secondary_item_id, $total_items, $format = 'string' ) {

    if ( 'user_poked' == $action || 'user_poked_back' == $action ) {
        
        
        $link           = bp_poke_get_poke_list_url();
        $logged_user_id = $secondary_item_id;
        $poked_by       = $item_id;
        $title          = __( 'poke', 'bp-poke' );
        
        $text = '';
		
        if( $total_items > 1 )
            $text = sprintf ( '%d people poked you',$total_items );
        else
            $text = sprintf( __( '%s poked you'), bp_core_get_user_displayname( $poked_by ) ) ;
           
      if( $format == 'string' )  
        return '<a href="' . $link . '" title="' . $title . '">'.$text . '</a>';
      
    
      return array(
          'link' => $link,
          'text' => $text
      );
    }

}

/**
 *  Is it poke action?
 * @return boolean
 */
function bp_poke_is_poke_action() {
    
    if( is_user_logged_in() && bp_is_my_profile() && bp_is_activity_component() && bp_is_current_action( BP_POKE_SLUG ) )
        return true;
    
    return false;
    
}

function bp_poke_poke( $user_id ) {
    
	global $bp; 

	$poked_by  = get_current_user_id();
	$component = $bp->poke->id;
	$action    = 'user_poked';


	//add data in user_meta table.

	$time = current_time('timestamp', 1);

	//get past poke details for this user
	$pokes = bp_get_user_meta( $user_id, 'pokes', true );

	//assuming one user can poke only once
	$pokes[$poked_by] = array( 'poked_by' => $poked_by, 'time' => $time );

	bp_update_user_meta( $user_id, 'pokes', $pokes );

	bp_core_add_notification( $poked_by, $user_id, $component, $action );
        
}


function bp_poke_poke_back( $user_id ) {
    
	global $bp; 
	$poked_by  = get_current_user_id();
	$component = $bp->poke->id;
	$action    = 'user_poked_back';

	 //we need to delete the pokes of the user whom the current user poked back, in current user;s meta.
	$logged_pokes = bp_get_user_meta( $poked_by, 'pokes', true );
	  //unset the poke from the user whom we just poked back
	  //delete the old poke info
	unset( $logged_pokes[$user_id] );

	  //now store back the updated pokes to current users meta

	bp_update_user_meta( $poked_by, 'pokes', $logged_pokes );

	//update for the user whom we have poked

	$time = current_time('timestamp', 1);

	//get past poke details for this user
	$pokes = bp_get_user_meta( $user_id, 'pokes', true );

	//assuming one user can poke only once
	$pokes[$poked_by] = array( 'poked_by' => $poked_by, 'time' => $time );

	bp_update_user_meta( $user_id, 'pokes', $pokes );

	bp_core_add_notification( $poked_by, $user_id, $component, $action );
        
}