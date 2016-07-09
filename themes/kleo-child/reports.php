<?php
/**
 * The template for displaying Reports
 *
 * The area of the page that contains comments and the comment form.
 *
 * @package WordPress
 * @subpackage Kleo
 * @since Kleo 1.0
 */

/*
 * If the current post is protected by a password and the visitor has not yet
 * entered the password we will return early without loading the comments.
 */
if ( post_password_required() || ! comments_open() ) {
	return;
}
global $bp; 
global $post;
 
$user=get_user_by("ID", bp_displayed_user_id());
$paged = (get_query_var('paged'))? get_query_var('paged') : 1;

$base_args=array(
	'numberposts'     => 5,		
	'orderby'         => 'post_date',
	'order'           => 'DESC',	
	'meta_key'        => '__post_id',
	'meta_value'      =>  $post->ID,
	'post_type'       => 'user_reports',	 
	'post_status'     => 'publish',
	'paged'			  =>  $paged
);

$reports=new WP_Query($base_args);
?>
<section class="container-wrap">
	<div class="container">
		<div id="buddypress" class="comments-area">

           

			<?php if ( $reports->have_posts() ) : ?>
			<div id="comments-list">
				
				<ul id="feedback-stream" data-postid="<?=$post->ID?>" data-type="be_load_more_reports" class="activity-list item-list">
					<?php while($reports->have_posts() ) : $reports->the_post(); ?>
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
										<?php $rating = unserialize(get_post_meta(get_the_ID(), 'user_reports_data', true))['rating']?>
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
					<?php endwhile; ?>
				</ul>				
        
                <!-- <div class="activity-timeline"></div> -->
			</div>
      
			<?php endif; // have_comments() ?>
			
		</div><!-- #comments -->
	</div>
</section>
