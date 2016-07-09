<?php
/**
 * @package WordPress
 * @subpackage Kleo
 * @author SeventhQueen <themesupport@seventhqueen.com>
 * @since Kleo 1.0
 */


/**
 * Kleo Child Theme Functions
 * Add custom code below
*/ 

require_once('gravity-limit.php');
require_once('includes/feedback.php');
require_once('includes/reports.php');
// Disable Clients, Testimonials or Portfolio post types

add_action( 'after_setup_theme', 'kleo_my_remove_actions' );
function kleo_my_remove_actions()
{
    global $kleo_post_types;
    /* Remove clients post type */
    remove_action( 'init', array( $kleo_post_types, 'setup_clients_post_type' ), 7 );
    /* Remove testimonials post type */
    remove_action( 'init', array( $kleo_post_types, 'setup_testimonials_post_type' ), 7 );
    /* Remove portfolio post type */
    remove_action( 'init', array( $kleo_post_types, 'setup_portfolio_post_type' ), 7 );
}


// If you don't want to see your user in the members directory you just need to add this snippet to child theme/functions.php

add_action('bp_ajax_querystring','sq7_radu_exclude_users',20,2);
 
function sq7_radu_exclude_users($qs=false,$object=false){
    //list of users to exclude
 
    $excluded_user = bbp_get_current_user_id();//comma separated ids of users whom you want to exclude
 
    if($object!='members')//hide for members only
        return $qs;
 
    $args=wp_parse_args($qs);
 
    //check if we are searching for friends list etc?, do not exclude in this case
    if(!empty($args['user_id'])||!empty($args['search_terms']))
        return $qs;
 
    if(!empty($args['exclude']))
        $args['exclude']=$args['exclude'].','.$excluded_user;
    else
        $args['exclude']=$excluded_user;
 
    $qs=build_query($args);
 
 
    return $qs;
 
}



// Hide Admin bar from non admin
function remove_admin_bar() {
	if( !is_super_admin() ) {
		add_filter( 'show_admin_bar', '__return_false' );
	}
}
add_action('wp', 'remove_admin_bar');



// Age validation on registration 
add_filter('gform_field_validation_3_26', 'verify_minimum_age', 10, 4);

function verify_minimum_age( $result, $value, $form, $field ){

		// date of birth is submitted in field 5 in the format YYYY-MM-DD
    $dob = rgpost('input_26');

		// this the minimum age requirement we are validating
    $minimum_age = 18;

    // calculate age in years like a human, not a computer, based on the same birth date every year
    //explode the date to get month, day and year
 		$birthDate = explode("/", $dob);

		//get age from date or birthdate
		if ( date("md", date("U", mktime(0, 0, 0, $birthDate[1], $birthDate[0], $birthDate[2]))) > date("md") ) {
  		$age =  ((date("Y") - $birthDate[2]) - 1);
  	}
  	else {
  		$age = date("Y") - $birthDate[2];
  	}
  

 		// is $age less than the $minimum_age?
    if( $age < $minimum_age ){
 
        // set the form validation to false if age is less than the minimum age
        $validation_result['is_valid'] = false;
 
        // find field with ID of 26 and mark it as failed validation
        foreach($form['fields'] as &$field){
 
            // NOTE: replace 5 with the field you would like to mark invalid
            if($field['id'] == '26'){
                $result['is_valid'] = false;
                $result['message'] = "Sorry, you must be at least $minimum_age years of age to join. You're $age years old.";
                break;
            }
 
        } 
        
    }

   
    return $result;

}


// Remove the "Private: " prefix on the forums
add_filter('private_title_format', 'ntwb_remove_private_title');
function ntwb_remove_private_title($title) {
    return '%s';
}

function add_scripts(){
    wp_deregister_script('gform_datepicker_init');
    wp_enqueue_script('custom_script', get_stylesheet_directory_uri().'/js/custom.js', array( 'jquery', 'jquery-ui-datepicker', 'gform_gravityforms' ), '1.0', true);      
    wp_enqueue_style('stylesheet-child', get_stylesheet_directory_uri().'/css/stylesheet.css');
}


add_action('wp_enqueue_scripts', 'add_scripts');

function user_permission_pages($template){
    if(!is_front_page() && !is_page('registration') && !is_user_logged_in() ){
        wp_redirect(home_url());       
    }
    return $template;
}

add_filter( 'template_include', 'user_permission_pages');

 

function add_custom_user_meta( $user_id, $feed, $entry, $user_pass ) { 
    
    mkdir(BP_AVATAR_UPLOAD_PATH.'/avatars/'.$user_id); 
    $ext = (substr(get_user_meta($user_id, 'profile_image', true), strripos(get_user_meta($user_id, 'profile_image', true), '.')));   
    copy(get_user_meta($user_id, 'profile_image', true),  BP_AVATAR_UPLOAD_PATH.'/avatars/'.$user_id.'/photo-bpfull'.$ext);
    copy(get_user_meta($user_id, 'profile_image', true),  BP_AVATAR_UPLOAD_PATH.'/avatars/'.$user_id.'/photo-bpthumb'.$ext );
    $add = '';
    foreach ($entry as $key => $value) {
        if ($key == 4){
            $add.= $value.' ';
        }
        else if($key == 10){
            $add.= $value.' ';
        }
        else if ($key == 11){
            $add.= $value;
        }        
    } 
    global $wpdb;     
    $url="http://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($add);
    $json = file_get_contents($url);
    $data = json_decode($json, TRUE);
    foreach ($data['results'][0]['address_components'] as $key => $value) {
       if(in_array('postal_code', $value['types'])){
            $postal_code=$value['long_name'];
       }
       if(in_array('administrative_area_level_2', $value['types'])){
            $state=$value['short_name'];
            $state_long=$value['long_name'];
       }
       if(in_array('country', $value['types'])){
            $country=$value['short_name'];
            $country_long=$value['long_name'];
       }
       if(in_array('country', $value['types'])){
            $country=$value['short_name'];
            $country_long=$value['long_name'];
       }
       if(in_array('locality',$value['types'])){
            $city=$value['long_name'];
       }
    }
    $data2 = array( 'member_id' => $user_id,
                    'lat'=>$data['results'][0]['geometry']['location']['lat'],
                    'long'=>$data['results'][0]['geometry']['location']['lng'],
                    'city' =>$city,
                    'zipcode' => $postal_code,
                    'address' => $data['results'][0]['formatted_address'],
                    'map_icon' => '_default.png',
                    'state' => $state,
                    'state_long' =>$state_long,
                    'country_long' => $country_long,
                    'country' => $country);

    $wpdb->insert( 'wppl_friends_locator', $data2);
}

add_action( 'gform_user_registered', 'add_custom_user_meta', 10, 4 );
function update_xprofile_custom( $user_id, $feed, $entry){
    // var_dump($_POST);
    // die;
    global $wpdb;  
    $add='';
    foreach ($_POST as $key => $value) {
        if ($key == "field_9"){
            $add.= $value.' ';
        }
        else if($key == "field_8"){
            $add.= $value.' ';
        }
        else if ($key == 'field_80'){
            $add.= $value;
        }        
    }  
    $url="http://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($add);
    $json = file_get_contents($url);
    $data = json_decode($json, TRUE);
    foreach ($data['results'][0]['address_components'] as $key => $value) {
       if(in_array('postal_code', $value['types'])){
            $postal_code=$value['long_name'];
       }
       if(in_array('administrative_area_level_2', $value['types'])){
            $state=$value['short_name'];
            $state_long=$value['long_name'];
       }
       if(in_array('country', $value['types'])){
            $country=$value['short_name'];
            $country_long=$value['long_name'];
       }
       if(in_array('country', $value['types'])){
            $country=$value['short_name'];
            $country_long=$value['long_name'];
       }
       if(in_array('locality',$value['types'])){
            $city=$value['long_name'];
       }
    }
    
    $data2 = array( 'member_id' => $user_id,
                    'lat'=>$data['results'][0]['geometry']['location']['lat'],
                    'long'=>$data['results'][0]['geometry']['location']['lng'],
                    'city' =>$city,
                    'zipcode' => $postal_code,
                    'address' => $data['results'][0]['formatted_address'],
                    'map_icon' => '_default.png',
                    'state' => $state,
                    'state_long' =>$state_long,
                    'country_long' => $country_long,
                    'country' => $country);

    $wpdb->update( 'wppl_friends_locator', $data2, array( 'member_id' => $user_id ));
}
add_action( 'xprofile_updated_profile', 'update_xprofile_custom', 10, 4 );



function feedback_button() {
    if(bbp_get_current_user_id()!=bp_displayed_user_id()):
    echo    '<div class="generic-button">
                <a href="#" class="" data-toggle="modal" data-target="#myModal" data-user_id="'.bp_displayed_user_id().'" title="">Leave Feedback</a>
            </div>';
    endif;
}
add_filter( 'bp_member_header_actions', 'feedback_button', 30  );

function boone_remove_blogs_nav() {
    bp_core_remove_nav_item('location');    
}
add_action( 'bp_setup_nav', 'boone_remove_blogs_nav', 15 );


function my_test_setup_nav() {
    global $bp;
    bp_core_new_nav_item( 
        array(  'name' => __( 'Feedback' ),
                'slug' => 'feedback',
                'default_subnav_slug' => 'feedback',
                'parent_url' => $bp->loggedin_user->domain . $bp->slug . '/',
                'parent_slug' => $bp->slug,
                'screen_function' => 'feedback_page_function_to_show_screen',
                'position' => 100 ) );
}
function feedback_page_function_to_show_screen() {    
    add_action( 'bp_template_title', 'feedback_page_function_to_show_screen_title' );
    add_action( 'bp_template_content', 'feedback_page_function_to_show_screen_content' );
    bp_core_load_template( apply_filters( 'feedback_page_function_to_show_screen', 'members/single/plugins' ) );
}
function feedback_page_function_to_show_screen_title() {
    echo 'Feedback';
}
function feedback_page_function_to_show_screen_content() {    
    get_template_part('feedback-profile');
}
add_action( 'bp_setup_nav', 'my_test_setup_nav' );

// icon-text