<?php
/**
 * The template for displaying the footer
 *
 * Contains footer content and the closing of the #main and #page div elements.
 *
 * @package WordPress
 * @subpackage Kleo
 * @since Kleo 1.0
 */
?>

			<?php
			/**
			 * After main part - action
			 */
			do_action('kleo_after_main');
			?>
			<div id="myModal" class="modal fade" role="dialog">
			  	<div class="modal-dialog">
			    	<!-- Modal content-->
				    <div class="modal-content">
				      	<div class="modal-header">
				        	<button type="button" class="close" data-dismiss="modal">&times;</button>
				        	<h4 class="modal-title">Leave Feedback</h4>
				      	</div>
				      	<div class="modal-body">

				      	<?php if(is_member_can_feed()): ?>
				      		<?=do_shortcode('[gravityform id=10 title=false description=false ajax=true tabindex=49]')?>
				      		<script type="text/javascript">
				      			jQuery(document).ready(function(){
				      				jQuery("#input_10_4").val(<?=bp_displayed_user_id()?>);
				      			});
				      		</script>
				      	<?php else: ?>
				      		You have already left feedback on this user
				      	<?php endif; ?>
				      	</div>
				      	<div class="modal-footer">
				        	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				      	</div>
				    </div>
				    <!-- Modal end-->
			  	</div>
			</div>
		</div><!-- #main -->
		
		<?php get_sidebar('footer');?>
	
		<?php 
		/**
		 * After footer hook
		 * @hooked kleo_go_up
		 * @hooked kleo_show_contact_form
		 */
		do_action('kleo_after_footer');
		?>

	</div><!-- #page -->

    <?php
    /**
     * After page hook
     * @hooked kleo_show_side_menu 10
     */
    do_action('kleo_after_page');
    ?>

	<!-- Analytics -->
	<?php echo sq_option('analytics', ''); ?>

	<?php wp_footer(); ?>


</body>
</html>