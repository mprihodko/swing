<?php

if ( ! class_exists( 'chatroomOptions' ) ) {
	class chatroomOptions {
			
		function __construct() {
			$this->create_chatroom_Options();
		}
		
		public function create_chatroom_Options() {
			require_once(MCR_PATH . "admin/admin-page-class.php");
			/**
			* configure your admin page
			*/
			$config = array(    
				'menu'           => 'settings',             //sub page to settings page
				'page_title'     => 'Chatroom options',       //The name of this page 
				'capability'     => 'edit_themes',         // The capability needed to view the page 
				'option_group'   => 'chatroom_options',       //the name of the option to create in the database
				'id'             => 'admin_page',   // meta box id, unique per page
				'fields'         => array(),    // list of fields (can be added by field arrays)
				'local_images'   => false,   // Use local or hosted images (meta box images for add/remove)
				'use_with_theme' => false //change path if used with theme set to true, false for a plugin or anything else for a custom path(default false).
			);  
			
			/**
			* instantiate your admin page
			*/
			$options_panel = new BF_Admin_Page_Class($config);
			$options_panel->OpenTabs_container('');
			
			/**
			* define your admin page tabs listing
			*/
			$options_panel->TabsListing(array(
			'links' => array(
			  'options_1' =>  __('Style Options','apc'),
			  'options_2' =>  __('Refresh Options','apc'),
			  'options_3' => __('Database Options','apc'),
			  //'options_4' => __('BuddyPress Options','apc'),
			  //'options_5' =>  __('Advanced Options','apc'),
			  'options_6' =>  __('Language Options','apc'),
			  'options_7' =>  __('Import Export','apc'),
			)
			));
			
			/**
			* Open admin page first tab
			*/
			$options_panel->OpenTab('options_1');
			
			/**
			* Add fields to your admin page first tab
			* 
			* Simple options:
			* input text, checbox, select, radio 
			* textarea
			*/
			//title
			$options_panel->Title(__("Style Options","apc"));
			//An optionl descrption paragraph
			//Color field
			$options_panel->addColor('cr_title_bg_color',array('name'=> __('Header background color.','apc'), 'std' => '', 'desc' => __('','apc')));
			$options_panel->addColor('cr_title_color',array('name'=> __('Header icon color','apc'), 'std' => '', 'desc' => __('','apc')));
			$options_panel->addColor('cr_title_color_hover',array('name'=> __('Header icon color on hover and active','apc'), 'std' => '', 'desc' => __('','apc')));
			$options_panel->addColor('cr_chat_bg_color',array('name'=> __('Chat body background color','apc'), 'std' => '', 'desc' => __('','apc')));
			$options_panel->addImage('cr_chat_bg_image',array('name'=> __('Chat body background image.','apc'),'preview_height' => '48px', 'preview_width' => '64px', 'desc' => __('','apc')));
			$options_panel->addRadio(
				'cr_chat_bg_pattern',
				array(
					'bg0'=>'<img class="bpc-radio-image" src="'.MCR_URL.'images/bg/bg0.png" />',
					'bg1'=>'<img class="bpc-radio-image" src="'.MCR_URL.'images/bg/bg1.png" />',
					'bg2'=>'<img class="bpc-radio-image" src="'.MCR_URL.'images/bg/bg2.png" />',
					'bg3'=>'<img class="bpc-radio-image" src="'.MCR_URL.'images/bg/bg3.png" />',
					'bg4'=>'<img class="bpc-radio-image" src="'.MCR_URL.'images/bg/bg4.png" />',
					'bg5'=>'<img class="bpc-radio-image" src="'.MCR_URL.'images/bg/bg5.png" />',
					'bg6'=>'<img class="bpc-radio-image" src="'.MCR_URL.'images/bg/bg6.png" />',
					'bg7'=>'<img class="bpc-radio-image" src="'.MCR_URL.'images/bg/bg7.png" />',
					'bg8'=>'<img class="bpc-radio-image" src="'.MCR_URL.'images/bg/bg8.png" />',
					'bg9'=>'<img class="bpc-radio-image" src="'.MCR_URL.'images/bg/bg9.png" />',
					'bg10'=>'<img class="bpc-radio-image" src="'.MCR_URL.'images/bg/bg10.png" />',
					'bg11'=>'<img class="bpc-radio-image" src="'.MCR_URL.'images/bg/bg11.png" />',
					'bg12'=>'<img class="bpc-radio-image" src="'.MCR_URL.'images/bg/bg12.png" />',
					'bg13'=>'<img class="bpc-radio-image" src="'.MCR_URL.'images/bg/bg13.png" />',
					'bg14'=>'<img class="bpc-radio-image" src="'.MCR_URL.'images/bg/bg14.png" />',
					'bg15'=>'<img class="bpc-radio-image" src="'.MCR_URL.'images/bg/bg15.png" />',
					'bg16'=>'<img class="bpc-radio-image" src="'.MCR_URL.'images/bg/bg16.png" />',
					'bg17'=>'<img class="bpc-radio-image" src="'.MCR_URL.'images/bg/bg17.png" />',
					'bg18'=>'<img class="bpc-radio-image" src="'.MCR_URL.'images/bg/bg18.png" />',
					'bg19'=>'<img class="bpc-radio-image" src="'.MCR_URL.'images/bg/bg19.png" />',
					'bg20'=>'<img class="bpc-radio-image" src="'.MCR_URL.'images/bg/bg20.png" />',
					'bg21'=>'<img class="bpc-radio-image" src="'.MCR_URL.'images/bg/bg21.png" />',
					'bg22'=>'<img class="bpc-radio-image" src="'.MCR_URL.'images/bg/bg22.png" />',
					'bg23'=>'<img class="bpc-radio-image" src="'.MCR_URL.'images/bg/bg23.png" />',
					'bg24'=>'<img class="bpc-radio-image" src="'.MCR_URL.'images/bg/bg24.png" />',
					'bg25'=>'<img class="bpc-radio-image" src="'.MCR_URL.'images/bg/bg25.png" />',
					'bg26'=>'<img class="bpc-radio-image" src="'.MCR_URL.'images/bg/bg26.png" />',
					'bg27'=>'<img class="bpc-radio-image" src="'.MCR_URL.'images/bg/bg27.png" />',
					'bg28'=>'<img class="bpc-radio-image" src="'.MCR_URL.'images/bg/bg28.png" />',
					'bg29'=>'<img class="bpc-radio-image" src="'.MCR_URL.'images/bg/bg29.png" />',
					'bg30'=>'<img class="bpc-radio-image" src="'.MCR_URL.'images/bg/bg30.png" />',
					'bg31'=>'<img class="bpc-radio-image" src="'.MCR_URL.'images/bg/bg31.png" />',
					'bg32'=>'<img class="bpc-radio-image" src="'.MCR_URL.'images/bg/bg32.png" />',
					'none'=>'none',
				),
				array(
					'name'=> __('Chat body background pattern','apc'),
					'std'=> array('none'), 
					'desc' => __('','apc')
				)
			);
			$options_panel->addColor('cr_placeholder_text_color',array('name'=> __('Start Chat text color','apc'), 'std' => '', 'desc' => __('','apc')));
			
			$options_panel->addColor('cr_start_chat_bg_color',array('name'=> __('Start Chat background color','apc'), 'std' => '', 'desc' => __('','apc')));
			$options_panel->addColor('cr_start_chat_bdr_color',array('name'=> __('Start Chat border color','apc'), 'std' => '', 'desc' => __('','apc')));
			
			$options_panel->addColor('cr_chat_text_color',array('name'=> __('Chat box text color ','apc'), 'std' => '', 'desc' => __('','apc')));
			$options_panel->addColor('cr_chatbox_bg_color',array('name'=> __('Chat box background color ','apc'), 'std' => '', 'desc' => __('','apc')));

			$options_panel->addCheckbox('enable_cr_chatbox_bg_opacity',array('name'=> __('Transparent chat background?','apc'), 'std' => false, 'desc' => __('Enable if you want transparent chat background.','apc')));
			//min numeric value
			$options_panel->addText('cr_chatbox_bg_opacity',
			array(
			  'name'     => __('Set background opacity ','apc'),
			  'std'      => .5,
			  'desc'     => __("Value must be between 0-1 (Example: .4, .5, .6 .7). Default: .5",'apc'),
			  'validate' => array(
				  'minvalue' => array('param' => 0,'message' => __("Must be numeric with a min value of 0",'apc'))
			  ),
			  'validate' => array(
				  'maxvalue' => array('param' => 1,'message' => __("Must be numeric with a Max value of 1",'apc'))
			  )
			)
			);
			
			//$options_panel->addParagraph(__("This is a simple paragraph","apc"));
			//text field
			//$options_panel->addText('text_field_id', array('name'=> __('My Text ','apc'), 'std'=> 'text', 'desc' => __('Simple text field description','apc')));
			//textarea field
			//$options_panel->addTextarea('textarea_field_id',array('name'=> __('My Textarea ','apc'), 'std'=> 'textarea', 'desc' => __('Simple textarea field description','apc')));
			//checkbox field
			
			//select field
			//$options_panel->addSelect('select_field_id',array('selectkey1'=>'Select Value1','selectkey2'=>'Select Value2'),array('name'=> __('My select ','apc'), 'std'=> array('selectkey2'), 'desc' => __('Simple select field description','apc')));
			//radio field
			
			/**
			* Close first tab
			*/   
			$options_panel->CloseTab();
			
			
			/**
			* Open admin page Second tab
			*/
			$options_panel->OpenTab('options_2');
			/**
			* Add fields to your admin page 2nd tab
			* 
			* Fancy options:
			*  typography field
			*  image uploader
			*  Pluploader
			*  date picker
			*  time picker
			*  color picker
			*/
			//title
			$options_panel->Title(__('Refresh Options','apc'));
			
			//is_numeric
			$options_panel->addText('chat_refresh_rate',
			array(
			  'name'     => __('Chat message refresh rate ','apc'),
			  'std'      => 5000,
			  'desc'     => __("Value is in millisecond (1000 ms = 1 sec). Default: 5000 ","apc"),
			  'validate' => array(
				  'numeric' => array('param' => '','message' => __("must be numeric value","apc"))
			  )
			)
			);
			//Typography field
			//$options_panel->addTypo('typography_field_id',array('name' => __("My Typography","apc"),'std' => array('size' => '14px', 'color' => '#000000', 'face' => 'arial', 'style' => 'normal'), 'desc' => __('Typography field description','apc')));
			//Image field
			//$options_panel->addImage('image_field_id',array('name'=> __('My Image ','apc'),'preview_height' => '120px', 'preview_width' => '440px', 'desc' => __('Simple image field description','apc')));
			//PLupload field
			//$options_panel->addPlupload('plupload_field_ID',array('name' => __('PlUpload Field','apc'), 'multiple' => true, 'desc' => __('Simple multiple image field description','apc')));  
			//date field
			//$options_panel->addDate('date_field_id',array('name'=> __('My Date ','apc'), 'desc' => __('Simple date picker field description','apc')));
			//Time field
			//$options_panel->addTime('time_field_id',array('name'=> __('My Time ','apc'), 'desc' => __('Simple time picker field description','apc')));
			
			
			/**
			* Close second tab
			*/ 
			$options_panel->CloseTab();
			
			
			
			/**
			* Open admin page 3rd tab
			*/
			$options_panel->OpenTab('options_3');
			/**
			* Add fields to your admin page 3rd tab
			* 
			* Editor options:
			*   WYSIWYG (tinyMCE editor)
			*  Syntax code editor (css,html,js,php)
			*/
			//title
			$options_panel->Title(__("Database Options","apc"));
			
			$options_panel->addCheckbox('enable_chat_cleanup',array('name'=> __('Delete database chat history? ','apc'), 'std' => false, 'desc' => __('Enable this to delete chat history from database','apc')));
			
			$options_panel->addRadio(
				'chat_cleanup_interval',
				array(
					'hourly'=>'Once hourly',
					'twicedaily'=>'Twice daily',
					'daily'=>'Once daily',
					'weekly'=>'Once weekly',
					'monthly'=>'Once Monthly',
					'yearly'=>'Once Yearly',
				),
				array(
					'name'=> __('Database cleanup interval','apc'),
					'std'=> array('monthly'), 
					'desc' => __('Select Chat cleanup interval from database','apc')
				)
			);
			
			//wysiwyg field
			//$options_panel->addWysiwyg('wysiwyg_field_id',array('name'=> __('My wysiwyg Editor ','apc'), 'desc' => __('wysiwyg field description','apc')));
			//code editor field
			//$options_panel->addCode('code_field_id',array('name'=> __('Code Editor ','apc'),'syntax' => 'php', 'desc' => __('code editor field description','apc')));
			/**
			* Close 3rd tab
			*/ 
			$options_panel->CloseTab();
			
			
			/**
			* Open admin page 4th tab
			*/
			
			//$options_panel->OpenTab('options_4');
			
			/**
			* Add fields to your admin page 4th tab
			* 
			* WordPress Options:
			*   Taxonomies dropdown
			*  posts dropdown
			*  Taxonomies checkboxes list
			*  posts checkboxes list
			*  
			*/
			//title
			//$options_panel->Title(__("BuddyPress Options","apc"));
			//taxonomy select field
			//$options_panel->addCheckbox('only_bp_friend',array('name'=> __('BuddyPress friend only? ','apc'), 'std' => false, 'desc' => __('If your website is BuddyPress based and want to enable chat only between friends then enable this.','apc')));
			//$options_panel->addTaxonomy('taxonomy_field_id',array('taxonomy' => 'category'),array('name'=> __('My Taxonomy Select','apc'),'class' => 'no-fancy','desc' => __('This field has a <pre>.no-fancy</pre> class which disables the fancy select2 functions','apc') ));
			//posts select field
			//$options_panel->addPosts('posts_field_id',array('args' => array('post_type' => 'post')),array('name'=> __('My Posts Select','apc'), 'desc' => __('posts select field description','apc')));
			//Roles select field
			//$options_panel->addRoles('roles_field_id',array(),array('name'=> __('My Roles Select','apc'), 'desc' => __('roles select field description','apc')));
			//taxonomy checkbox field
			//$options_panel->addTaxonomy('taxonomy2_field_id',array('taxonomy' => 'category','type' => 'checkbox_list'),array('name'=> __('My Taxonomy Checkboxes','apc'), 'desc' => __('taxonomy checkboxes field description','apc')));
			//posts checkbox field
			//$options_panel->addPosts('posts2_field_id',array('post_type' => 'post','type' => 'checkbox_list'),array('name'=> __('My Posts Checkboxes','apc'), 'class' => 'no-toggle','desc' => __('This field has a <pre>.no-toggle</pre> class which disables the fancy Iphone like toggle','apc')));
			//Roles checkbox field
			//$options_panel->addRoles('roles2_field_id',array('type' => 'checkbox_list' ),array('name'=> __('My Roles Checkboxes','apc'), 'desc' => __('roles checboxes field description','apc')));
			
			
			/**
			* Close 4th tab
			*/
			
			//$options_panel->CloseTab();
			
			/**
			* Open admin page 5th tab
			*/
			//$options_panel->OpenTab('options_5');
			//title
			//$options_panel->Title(__("Advanced Options","apc"));
			
			//sortable field
			//$options_panel->addSortable('sortable_field_id',array('1' => 'One','2'=> 'Two', '3' => 'three', '4'=> 'four'),array('name' => __('My Sortable Field','apc'), 'desc' => __('Sortable field description','apc')));
			
			/*
			* To Create a reapeater Block first create an array of fields
			* use the same functions as above but add true as a last param
			
			$repeater_fields[] = $options_panel->addText('re_text_field_id',array('name'=> __('My Text ','apc')),true);
			$repeater_fields[] = $options_panel->addTextarea('re_textarea_field_id',array('name'=> __('My Textarea ','apc')),true);
			$repeater_fields[] = $options_panel->addImage('image_field_id',array('name'=> __('My Image ','apc')),true);
			$repeater_fields[] = $options_panel->addCheckbox('checkbox_field_id',array('name'=> __('My Checkbox  ','apc')),true);
			*/
			/*
			* Then just add the fields to the repeater block
			*/
			//repeater block
			//$options_panel->addRepeaterBlock('re_',array('sortable' => true, 'inline' => true, 'name' => __('This is a Repeater Block','apc'),'fields' => $repeater_fields, 'desc' => __('Repeater field description','apc')));
			
			/**
			* To Create a Conditional Block first create an array of fields (just like a repeater block
			* use the same functions as above but add true as a last param
			
			$Conditinal_fields[] = $options_panel->addText('con_text_field_id',array('name'=> __('My Text ','apc')),true);
			$Conditinal_fields[] = $options_panel->addTextarea('con_textarea_field_id',array('name'=> __('My Textarea ','apc')),true);
			$Conditinal_fields[] = $options_panel->addImage('con_image_field_id',array('name'=> __('My Image ','apc')),true);
			$Conditinal_fields[] = $options_panel->addCheckbox('con_checkbox_field_id',array('name'=> __('My Checkbox  ','apc')),true);
			*/
			/**
			* Then just add the fields to the repeater block
			
			//conditinal block 
			$options_panel->addCondition('conditinal_fields',
			  array(
				'name'   => __('Enable conditinal fields? ','apc'),
				'desc'   => __('<small>Turn ON if you want to enable the <strong>conditinal fields</strong>.</small>','apc'),
				'fields' => $Conditinal_fields,
				'std'    => false
			  ));
			*/
			/**
			* Close 5th tab
			*/
			//$options_panel->CloseTab();
			
			
			/**
			* Open admin page 6th tab
			*/
			
			$options_panel->OpenTab('options_6');
			
			$options_panel->addText('cr_lg_pls_login', array('name'=> __('Please login to chat','apc'), 'std'=> 'Please login to chat', 'desc' => __('','apc')));
			$options_panel->addText('cr_lg_common_room', array('name'=> __('Common chat room','apc'), 'std'=> 'Common chat room', 'desc' => __('','apc')));
			$options_panel->addText('cr_lg_group_room', array('name'=> __('Group chat room','apc'), 'std'=> 'Group chat room', 'desc' => __('','apc')));

			$options_panel->addText('cr_lg_group_chat', array('name'=> __('Group chat','apc'), 'std'=> 'Group chat', 'desc' => __('','apc')));
			$options_panel->addText('cr_lg_private_chat', array('name'=> __('Private chat','apc'), 'std'=> 'Private chat', 'desc' => __('','apc')));			
			$options_panel->addText('cr_lg_profile', array('name'=> __('Profile','apc'), 'std'=> 'Profile', 'desc' => __('','apc')));
			
			$options_panel->addText('cr_lg_search_friends', array('name'=> __('Search...','apc'), 'std'=> 'Search...', 'desc' => __('','apc')));
			$options_panel->addText('cr_lg_refresh', array('name'=> __('Refresh','apc'), 'std'=> 'Refresh', 'desc' => __('','apc')));
			$options_panel->addText('cr_lg_monline', array('name'=> __('Members online','apc'), 'std'=> 'Members online', 'desc' => __('','apc')));
			$options_panel->addText('cr_lg_glist', array('name'=> __('Group List','apc'), 'std'=> 'Group List', 'desc' => __('','apc')));
			
			$options_panel->addText('cr_lg_submit', array('name'=> __('Submit','apc'), 'std'=> 'Submit', 'desc' => __('','apc')));
			$options_panel->addText('cr_lg_no_result', array('name'=> __('No results','apc'), 'std'=> 'No results', 'desc' => __('','apc')));
			$options_panel->addText('cr_lg_nom_online', array('name'=> __('No member online','apc'), 'std'=> 'No member online.', 'desc' => __('','apc')));
			$options_panel->addText('cr_lg_not_member', array('name'=> __('You are not a member of any group.','apc'), 'std'=> 'You are not a member of any group.', 'desc' => __('','apc')));
			
			
			$options_panel->addText('cr_lg_chat', array('name'=> __('Start Chat','apc'), 'std'=> 'Start Chat', 'desc' => __('','apc')));
			$options_panel->addText('cr_lg_plogin', array('name'=> __('Login to chatroom','apc'), 'std'=> 'Login to chatroom', 'desc' => __('','apc')));
			$options_panel->addText('cr_lg_psignup', array('name'=> __('Not a member? Please sign up.','apc'), 'std'=> 'Not a member? Please sign up.', 'desc' => __('','apc')));
			$options_panel->addText('cr_lg_uname', array('name'=> __('Username','apc'), 'std'=> 'Username', 'desc' => __('','apc')));
			$options_panel->addText('cr_lg_fname', array('name'=> __('Full name','apc'), 'std'=> 'Full name', 'desc' => __('','apc')));
			$options_panel->addText('cr_lg_email', array('name'=> __('Email','apc'), 'std'=> 'Email', 'desc' => __('','apc')));
			$options_panel->addText('cr_lg_pass', array('name'=> __('Password','apc'), 'std'=> 'Password', 'desc' => __('','apc')));
			$options_panel->addText('cr_lg_cpass', array('name'=> __('Confirm password','apc'), 'std'=> 'Confirm password', 'desc' => __('','apc')));
			$options_panel->addText('cr_lg_mber', array('name'=> __('Remember me','apc'), 'std'=> 'Remember me', 'desc' => __('','apc')));
			$options_panel->addText('cr_lg_login', array('name'=> __('Log in','apc'), 'std'=> 'Log in', 'desc' => __('','apc')));
			$options_panel->addText('cr_lg_reg', array('name'=> __('Register','apc'), 'std'=> 'Register', 'desc' => __('','apc')));
			$options_panel->addText('cr_lg_lostp', array('name'=> __('Lost your password?','apc'), 'std'=> 'Lost your password?', 'desc' => __('','apc')));
			$options_panel->addText('cr_lg_newss', array('name'=> __('New to chatroom?','apc'), 'std'=> 'New to chatroom?', 'desc' => __('','apc')));
			$options_panel->addText('cr_lg_preg', array('name'=> __('Please register','apc'), 'std'=> 'Please register', 'desc' => __('','apc')));
			$options_panel->addText('cr_lg_areg', array('name'=> __('Already member? Please login.','apc'), 'std'=> 'Already member? Please login.', 'desc' => __('','apc')));
			
			$options_panel->addText('cr_lg_cgroup', array('name'=> __('Create Group','apc'), 'std'=> 'Create Group', 'desc' => __('','apc')));
			$options_panel->addText('cr_lg_create', array('name'=> __('Create','apc'), 'std'=> 'Create', 'desc' => __('','apc')));
			
			$options_panel->addText('cr_lg_g_name', array('name'=> __('Group Name','apc'), 'std'=> 'Group Name', 'desc' => __('','apc')));
			$options_panel->addText('cr_lg_g_desc', array('name'=> __('Group Description','apc'), 'std'=> 'Group Description', 'desc' => __('','apc')));
			$options_panel->addText('cr_lg_pu_group', array('name'=> __('Public group','apc'), 'std'=> 'Public group', 'desc' => __('','apc')));
			$options_panel->addText('cr_lg_pv_group', array('name'=> __('Private group','apc'), 'std'=> 'Private group', 'desc' => __('','apc')));
			$options_panel->addText('cr_lg_ph_group', array('name'=> __('Hidden group','apc'), 'std'=> 'Hidden group', 'desc' => __('','apc')));
			$options_panel->addText('cr_lg_am_group', array('name'=> __('All members','apc'), 'std'=> 'All members', 'desc' => __('','apc')));
			$options_panel->addText('cr_lg_amo_group', array('name'=> __('Admins and mods only','apc'), 'std'=> 'Admins and mods only', 'desc' => __('','apc')));
			$options_panel->addText('cr_lg_ao_group', array('name'=> __('Admins only','apc'), 'std'=> 'Admins only', 'desc' => __('','apc')));
			
			
			$options_panel->addText('cr_lg_email_exists', array('name'=> __('User with this email already registered.','apc'), 'std'=> 'User with this email already registered.', 'desc' => __('','apc')));
			$options_panel->addText('cr_lg_invalid_user', array('name'=> __('Invalid email address.','apc'), 'std'=> 'Invalid email address.', 'desc' => __('','apc')));
			$options_panel->addText('cr_lg_invalid_user', array('name'=> __('Invalid username.','apc'), 'std'=> 'Invalid username.', 'desc' => __('','apc')));
			$options_panel->addText('cr_lg_user_exits', array('name'=> __('Username alreay exists.','apc'), 'std'=> 'Username alreay exists.', 'desc' => __('','apc')));
			$options_panel->addText('cr_lg_reg_failed', array('name'=> __('Registration failed.','apc'), 'std'=> 'Registration failed.', 'desc' => __('','apc')));
			$options_panel->addText('cr_lg_reg_success', array('name'=> __('Successful! Please wait...','apc'), 'std'=> 'Successful! Please wait...', 'desc' => __('','apc')));
			
			
			$options_panel->addText('cr_lg_db_error', array('name'=> __('Database Error','apc'), 'std'=> 'Database Error', 'desc' => __('','apc')));
			$options_panel->addText('cr_lg_mx_image', array('name'=> __('Max image size is 4MB.','apc'), 'std'=> 'Max image size is 4MB.', 'desc' => __('','apc')));
			$options_panel->addText('cr_lg_allow_image', array('name'=> __('Only jpeg, jpg, gif, png and pjpeg image is allowed.','apc'), 'std'=> 'Only jpeg, jpg, gif, png and pjpeg image is allowed.', 'desc' => __('','apc')));
			$options_panel->addText('cr_lg_mx_file', array('name'=> __('Max file size is 12MB.','apc'), 'std'=> 'Max file size is 12MB.', 'desc' => __('','apc')));
			$options_panel->addText('cr_lg_allowed_file', array('name'=> __('File formet is not allowed.','apc'), 'std'=> 'File formet is not allowed.', 'desc' => __('','apc')));
			
			
			/**
			* Close 6th tab There are no friends.
			*/
			$options_panel->CloseTab();
			
			/**
			* Open admin page 7th tab
			*/
			$options_panel->OpenTab('options_7');
			
			//title
			$options_panel->Title(__("Import Export","apc"));
			
			/**
			* add import export functionallty
			*/
			$options_panel->addImportExport();
			
			/**
			* Close 7th tab
			*/
			$options_panel->CloseTab();
			$options_panel->CloseTab();
			
			//Now Just for the fun I'll add Help tabs
			/*
			$options_panel->HelpTab(array(
			'id'      =>'tab_id',
			'title'   => __('My help tab title','apc'),
			'content' =>'<p>'.__('This is my Help Tab content','apc').'</p>'
			));			
			*/
		}
	
	}
	new chatroomOptions();
}

?>