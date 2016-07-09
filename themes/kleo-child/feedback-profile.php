<?php 

/*Template Name: Feedback*/


global $bp; 


$user=get_user_by("ID", bp_displayed_user_id());
$paged = (get_query_var('paged'))? get_query_var('paged') : 1;

$base_args=array(
	'numberposts'     => 2,		
	'orderby'         => 'post_date',
	'order'           => 'DESC',	
	'meta_key'        => 'user_id',
	'meta_value'      =>  bp_displayed_user_id(),
	'post_type'       => 'user_feedbacks',	 
	'post_status'     => 'publish',
	'paged'			  =>  $paged
);

$feedbacks=new WP_Query($base_args);

?>
<ul id="feedback-stream" data-type="be_ajax_load_more" class="activity-list item-list">

	<?php if($feedbacks->have_posts()): ?>

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


		<?php endwhile; ?>
	<?php else: ?>
		<p>There is currently no feedback on this user.</p>
	<?php endif; ?>

	

</ul>
<?php wp_reset_query(); ?>
