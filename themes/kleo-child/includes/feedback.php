<?php 

add_action( 'admin_enqueue_scripts', 'enqueue_admin_js' );

function enqueue_admin_js(){    
    wp_enqueue_style( 'custom-css',  get_stylesheet_directory_uri().'/css/admin-stylesheet.css' );
}


add_action('init', 'register_user_feedbacks');
function register_user_feedbacks(){
	$args = array(
		'label'  => 'User Feedback',
		'labels' => array(
			'name'               => 'Users Feedbacks',
			'singular_name'      => 'User Feedback',
			'add_new'            => 'Add New User Feedback',
			'add_new_item'       => 'Add New User Feedback', 
			'edit_item'          => 'Edit User Feedback',
			'new_item'           => 'New User Feedback',
			'view_item'          => 'View User Feedback', 
			'search_items'       => 'Search Feedbacks', 
			'not_found'          => 'Feedbacks not found', 
			'not_found_in_trash' => 'Feedbacks not found in trash',		
			'menu_name'          => 'User Feedback', // название меню
		),		
		'public'              => true,		 
		'exclude_from_search' => true,		
		'menu_position'       => 71,
		'menu_icon'           => "dashicons-format-chat", 		 
		'hierarchical'        => false,		
		'supports'            => array('editor'),
		'taxonomies'          => array(),
		'has_archive'         => true,
		'rewrite'             => true,
		'query_var'           => true,
		'show_in_nav_menus'   => false,
	);

	register_post_type('user_feedbacks', $args );
}

 
function feedback_add_custom_box() {
	$screens = array('user_feedbacks');
	foreach ( $screens as $screen ){
		add_meta_box( 'user_feedback_data', 'Feedback Data', 'feedback_meta_box_callback', $screen );
	}
}
add_action('add_meta_boxes', 'feedback_add_custom_box');

 
function feedback_meta_box_callback() {
	
	$post_id=null;
	if(isset($_GET['post'])){
		$post_id=$_GET['post'];
	}
	if(get_post_meta($post_id, 'user_feedback_data', true)){
		$feed_data=unserialize(get_post_meta($post_id, 'user_feedback_data', true));
	}
	if(isset($feed_data['author_id'])){
		$user=get_user_by("ID", $feed_data['author_id']);
	} 
	wp_nonce_field("edit_uf", "edit_uf");
	?>
	<input type='hidden' name='user_id' value='<?=isset($feed_data['user_id'])? $feed_data['user_id'] : '' ?>'>
	<input type='hidden' name='rating' value='<?=isset($feed_data['rating'])? $feed_data['rating'] : '' ?>'>
	<input type='hidden' name='author_id' value='<?=isset($feed_data['author_id'])? $feed_data['author_id'] : '' ?>'>
	<span class='meta-block-rating'>Rating: <b><?=isset($feed_data['rating'])? $feed_data['rating'] : '' ?></b></span>
	<span class='meta-block-rating'>Author: <b><?=isset($user)? $user->first_name." ".$user->last_name : '' ?></b></span>
	<?php
}

 
function feedback_save_postdata( $post_id ) {
	 
	if ( ! wp_verify_nonce( $_POST['edit_uf'], 'edit_uf' ) )
		return $post_id;
	 
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
		return $post_id;
	 
	if ( 'user_feedbacks' != $_POST['post_type'] && ! current_user_can( 'edit_page', $post_id ) ) {
		  return $post_id;
	} elseif( ! current_user_can( 'edit_post', $post_id ) ) {
		return $post_id;
	}

	 
	if ( ! isset( $_POST['user_id'] ) || ! isset( $_POST['rating'] ) || !isset( $_POST['author_id'] ) )
		return;

	
	$user_id = intval( $_POST['user_id'] );
	$rating  = intval( $_POST['rating'] );
	$author_id = intval( $_POST['author_id']);
	if($user_id && $rating && $author_id){
		$uf_data['user_id']=$user_id;
		$uf_data['rating']=$rating;
		$uf_data['author_id']=$author_id;
	}

	update_post_meta( $post_id, 'user_feedback_data', serialize($uf_data) );
}

add_action( 'save_post', 'feedback_save_postdata' );

add_action( 'gform_after_submission_10', 'set_post_content', 10, 2 );
function set_post_content( $entry, $form ) {
	
	$user_id = intval( $entry[4] );
	$rating  = intval( $entry[3] );
	$author_id = intval( $entry['created_by']);
	if($user_id && $rating && $author_id){
		$uf_data['user_id']=$user_id;
		$uf_data['rating']=$rating;
		$uf_data['author_id']=$author_id;

		$post_data = array(
		  'post_title'    => 'Feedback '.$entry['date_created'],
		  'post_content'  => sanitize_text_field($entry[2]),
		  'post_status'   => 'pending',
		  'post_author'   => $entry['created_by'],
		  'post_type'	  => 'user_feedbacks',
		  'meta_input'    => array( 'user_feedback_data'=>serialize($uf_data), 'user_id'=>$user_id,  'author_id'=>$author_id)
		);
		$post_id = wp_insert_post( $post_data );	
	}
}

function is_member_can_feed(){
	global $bp;
	$args = query_posts(  							
							array(
								'post_type' => 'user_feedbacks',
								'meta_query' => array( 
									'relation' => 'AND',
									array(
									    'key'   => 'user_id',
									    'value' => bp_displayed_user_id(),
									    'compare' => '='
									),
									array(
										'key'   => 'author_id',
									    'value' => $bp->loggedin_user->id,
									    'compare' => '='
									) 
								)
								
							)
						);
	wp_reset_postdata();
	if(count($args)>0){
		return false;
	}else{
		return true;
	}	
}

add_action( 'wp_ajax_be_ajax_load_more', 'be_ajax_load_more' );
add_action( 'wp_ajax_nopriv_be_ajax_load_more', 'be_ajax_load_more' );

function be_ajax_load_more() {
	check_ajax_referer( 'be-load-more-nonce', 'nonce' );

	$base_args=array(
		'numberposts'     => 2,		
		'orderby'         => 'post_date',
		'order'           => 'DESC',	
		'meta_key'        => 'user_id',
		'meta_value'      =>  bp_displayed_user_id(),
		'post_type'       => 'user_feedbacks',	 
		'post_status'     => 'publish',
		'paged'			  =>  $_POST['page']
	);
	ob_start();
	$feedbacks = new WP_Query( $base_args );
		if($feedbacks->have_posts()): ?>

			<?php while ($feedbacks->have_posts()): $feedbacks->the_post() ?>

				<?php $author=get_user_by("ID", get_post_meta(get_the_ID(), 'author_id', true)); ?>
				

					<li class="friends friendship_created activity-item mini animated animate-when-almost-visible bottom-to-top start-animation" id="activity-174">
						
						<!-- avatar -->
						<div class="activity-avatar rounded">				
							<a href="<?=bp_core_get_user_domain($author->ID)?>" title="Profile photo <?=bp_core_get_user_displayname($author->ID)?>">
								<?php bp_activity_avatar( 'user_id=' . $author->ID ); ?>				
							</a>	    
						</div>
						<!-- avatar -->
						

						<div class="activity-content">			
							<div class="activity-header">

								<!-- name -->
								<p>
									<?php $rating = unserialize(get_post_meta(get_the_ID(), 'user_feedback_data', true))['rating']?>
									<a href="<?=bp_core_get_user_domain($author->ID) ?>" title="<?=bp_core_get_user_displayname($author->ID)?>"><?=bp_core_get_user_displayname($author->ID)?> </a>	
									<? the_title() ?>
								</p>
								<!-- name -->

								<!-- stars -->
								<div class="feedback-stars">
									<div class="empty-stars">
										<?php for($i=0; $i<5; $i++){ ?>
											<i class="fa fa-2x fa-star-o" aria-hidden="true"></i>
										<?php } ?>	
									</div>
									<div class="active-stars">
										<?php for($i=0; $i<5; $i++){ ?>
											<?php if($i<$rating){ ?>
												<i class="fa fa-2x fa-star" aria-hidden="true"></i>
											<?php }else{ ?>	
												<i class="fa fa-2x fa-star-o" aria-hidden="true"></i>
											<?php } ?>		
										<?php } ?>	
									</div>
								</div>
								<!-- stars -->

							</div>	    
						</div>

						<!-- feedback content -->
							<div class="activity-comments">
								<?php the_content() ?>
							</div>
						<!-- feedback content -->

						<div class="activity-timeline"></div>
					</li>


			<?php endwhile; 

		endif; 
 	wp_reset_postdata();

	$data = ob_get_clean();
	
	wp_send_json_success( $data );

	wp_die();
}

function be_load_more_js() {
	$pagename = get_query_var('pagename');

	if($pagename=="feedback"):
		global $wp_query;
		$args = array(
			'nonce' => wp_create_nonce( 'be-load-more-nonce' ),
			'url'   => admin_url( 'admin-ajax.php' ),
			'query' => $wp_query->query,
		);
				
		wp_enqueue_script( 'be-load-more', get_stylesheet_directory_uri() . '/js/load-more.js', array( 'jquery' ), '1.0', true );
		wp_localize_script( 'be-load-more', 'beloadmore', $args );
	endif;
}
add_action( 'wp_enqueue_scripts', 'be_load_more_js' );
