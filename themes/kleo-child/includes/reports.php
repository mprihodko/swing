<?php 

add_action('init', 'register_user_reports');
function register_user_reports(){
	$args = array(
		'label'  => 'User Reports',
		'labels' => array(
			'name'               => 'Users Reports',
			'singular_name'      => 'User Reports',
			'add_new'            => 'Add New User Reports',
			'add_new_item'       => 'Add New User Reports', 
			'edit_item'          => 'Edit User Reports',
			'new_item'           => 'New User Reports',
			'view_item'          => 'View User Reports', 
			'search_items'       => 'Search Reports', 
			'not_found'          => 'Reports not found', 
			'not_found_in_trash' => 'Reports not found in trash',		
			'menu_name'          => 'User Reports', // название меню
		),		
		'public'              => true,		 
		'exclude_from_search' => true,		
		'menu_position'       => 72,
		'menu_icon'           => "dashicons-format-chat", 		 
		'hierarchical'        => false,		
		'supports'            => array('editor'),
		'taxonomies'          => array(),
		'has_archive'         => true,
		'rewrite'             => true,
		'query_var'           => true,
		'show_in_nav_menus'   => false,
	);

	register_post_type('user_reports', $args );
}

function reports_add_custom_box() {
	$screens = array('user_reports');
	foreach ( $screens as $screen ){
		add_meta_box( 'user_reports_data', 'Report Data', 'reports_meta_box_callback', $screen );
	}
}
add_action('add_meta_boxes', 'reports_add_custom_box');

function reports_meta_box_callback() {
	
	$post_id=null;
	if(isset($_GET['post'])){
		$post_id=$_GET['post'];
	}
	if(get_post_meta($post_id, 'user_reports_data', true)){
		$feed_data=unserialize(get_post_meta($post_id, 'user_reports_data', true));
	}
	if(isset($feed_data['author_id'])){
		$user=get_user_by("ID", $feed_data['author_id']);
	} 
	wp_nonce_field("edit_uf", "edit_uf");
	?>
	<input type='hidden' name='__post_id' value='<?=isset($feed_data['__post_id'])? $feed_data['__post_id'] : '' ?>'>	
	<input type='hidden' name='author_id' value='<?=isset($feed_data['author_id'])? $feed_data['author_id'] : '' ?>'>	
	<span class='meta-block-rating'>Author: <b><?=isset($user)? $user->first_name." ".$user->last_name : '' ?></b></span>
	<?php
}

function reports_save_postdata( $post_id ) {
	 
	if ( ! wp_verify_nonce( $_POST['edit_uf'], 'edit_uf' ) )
		return $post_id;
	 
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
		return $post_id;
	 
	if ( 'user_reports' != $_POST['post_type'] && ! current_user_can( 'edit_page', $post_id ) ) {
		  return $post_id;
	} elseif( ! current_user_can( 'edit_post', $post_id ) ) {
		return $post_id;
	}

	if ( ! isset( $_POST['__post_id'] ) || !isset( $_POST['author_id'] ) )
		return;

	$__post_id = intval( $_POST['__post_id'] ); 
	$author_id = intval( $_POST['author_id']);

	if($__post_id  && $author_id){
		$uf_data['__post_id']=$__post_id;		 
		$uf_data['author_id']=$author_id;
	}

	update_post_meta( $post_id, 'user_reports_data', serialize($uf_data) );
	update_post_meta( $post_id, '__post_id', $__post_id );
	update_post_meta( $post_id, 'author_id', $author_id );
}

add_action( 'save_post', 'reports_save_postdata' );


add_action( 'gform_after_submission_11', 'add_new_report', 10, 2 );
function add_new_report( $entry, $form ) {
	
	$__post_id = intval( $entry[1] );	 
	$author_id = intval( $entry['created_by']);
	if($__post_id && $author_id){
		$uf_data['__post_id']=$__post_id;		 
		$uf_data['author_id']=$author_id;

		$post_data = array(
		  'post_title'    => 'Report '.$entry['date_created'],
		  'post_content'  => sanitize_text_field($entry[3]),
		  'post_status'   => 'pending',
		  'post_author'   => $entry['created_by'],
		  'post_type'	  => 'user_reports',
		  'meta_input'    => array('user_reports_data'=>serialize($uf_data), '__post_id'=>$__post_id,  'author_id'=>$author_id)
		);
		$post_id = wp_insert_post( $post_data );	
	}
}


add_action( 'wp_ajax_be_load_more_reports', 'be_load_more_reports' );
add_action( 'wp_ajax_nopriv_be_load_more_reports', 'be_load_more_reports' );

function be_load_more_reports() {
	check_ajax_referer( 'be-load-more-nonce', 'nonce' );
    
	$base_args=array(
		'numberposts'     => 5,		
		'orderby'         => 'post_date',
		'order'           => 'DESC',	
		'meta_key'        => '__post_id',
		'meta_value'      =>  $_POST['__post_id'],
		'post_type'       => 'user_reports',	 
		'post_status'     => 'publish',
		'paged'			  =>  $_POST['page']
	);

	ob_start();
	$reports = new WP_Query( $base_args );
	
		if($reports->have_posts()):  ?>

			<?php while ($reports->have_posts()): $reports->the_post() ?>

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

								 

							</div>	    
						</div>

						<!-- report content -->
							<div class="activity-comments">
								<?php the_content() ?>
							</div>
						<!-- report content -->

						<div class="activity-timeline"></div>
					</li>


			<?php endwhile; 

		endif; 
 	wp_reset_postdata();

	$data = ob_get_clean();
	
	wp_send_json_success( $data );

	wp_die();
}

function be_load_more_js_reports() {
	

	if(is_singular('post') ||  is_singular('speedy-dates') || is_singular('parties') || is_singular('voyeurs_den')):
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
add_action( 'wp_enqueue_scripts', 'be_load_more_js_reports' );