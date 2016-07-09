<?php
/*
Plugin Name: BuddyPress Friends Only Activity Stream
Plugin URI: http://untame.net
Description: This plugin limits the activity stream for general users to postings from their friends only.
Version: 1.0
Requires at least: WordPress 2.9.1 / BuddyPress 1.2
Tested up to: WordPress 2.9.2 / BuddyPress 1.2
License: GNU/GPL 2
Author: Sarah Gooding 
Author URI: http://untame.net
*/

/*** Make sure BuddyPress is loaded ********************************/
if ( !function_exists( 'bp_core_install' ) ) {
	require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
	if ( is_plugin_active( 'buddypress/bp-loader.php' ) )
		require_once ( WP_PLUGIN_DIR . '/buddypress/bp-loader.php' );
	else
		return;
}

function my_is_friend_check( $friend_id = false) {
global $bp;

if ( is_site_admin() )
return true;

if ( !is_user_logged_in() )
return false;

if (!$friend_id) {
$potential_friend_id = $bp->displayed_user->id;
} else {
$potential_friend_id = $friend_id;
}

if ( $bp->loggedin_user->id == $potential_friend_id )
return false;

if (friends_check_friendship_status($bp->loggedin_user->id, $potential_friend_id) == 'is_friend')
return true;

return false;
}

function my_denied_activity_nonfriends( $a, $activities ) {

//if admin we want to know
if ( is_site_admin() )
return $activities;

foreach ( $activities->activities as $key => $activity ) {
/* if member of a group – we want the activity even if nonfriend */
if ( $activity->component != 'groups' && $activity->user_id != 0 && !my_is_friend_check($activity->user_id) && !my_is_atme_check($activity->content) ) {

unset( $activities->activities[$key] );

$activities->activity_count = $activities->activity_count-1;
$activities->total_activity_count = $activities->total_activity_count-1;
$activities->pag_num = $activities->pag_num -1;

}
}

/* Renumber the array keys to account for missing items */
$activities_new = array_values( $activities->activities );
$activities->activities = $activities_new;

return $activities;
}
add_action('bp_has_activities', 'my_denied_activity_nonfriends', 10, 2 );

function my_is_atme_check( $content ) {
global $bp;

if ( !is_user_logged_in() )
return false;

if (!$content)
return false;

$pattern = '/[@]+([A-Za-z0-9-_]+)/';
preg_match_all( $pattern, $content, $usernames );

/* Make sure there’s only one instance of each username */
if ( !$usernames = array_unique( $usernames[1] ) )
return false;

if ( in_array( bp_core_get_username( $bp->loggedin_user->id ), $usernames ) )
return true;

return false;
}
?>