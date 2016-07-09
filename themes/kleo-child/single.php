<?php
/**
 * The Template for displaying all single posts
 *
 * @package WordPress
 * @subpackage Kleo
 * @since Kleo 1.0
 */

get_header(); ?>

<?php
//Specific class for post listing */
if ( kleo_postmeta_enabled() ) {
	$meta_status = ' with-meta';
	if ( sq_option( 'blog_single_meta', 'left' ) == 'inline' ) {
		$meta_status .= ' inline-meta';
	}
	add_filter( 'kleo_main_template_classes', create_function( '$cls','$cls .= "' . $meta_status . '"; return $cls;' ) );
}

/* Related posts logic */
$related = sq_option( 'related_posts', 1 );
if ( ! is_singular('post') ) {
    $related = sq_option( 'related_custom_posts', 0 );
}
//post setting
if(get_cfield( 'related_posts') != '' ) {
	$related = get_cfield( 'related_posts' );
}
?>

<?php get_template_part( 'page-parts/general-title-section' ); ?>

<?php get_template_part( 'page-parts/general-before-wrap' );?>

<?php /* Start the Loop */ ?>
<?php while ( have_posts() ) : the_post(); ?>

    <?php get_template_part( 'content', get_post_format() ); ?>

		<?php get_template_part( 'page-parts/posts-social-share' ); ?>

		<?php 
		if( $related == 1 ) {
			get_template_part( 'page-parts/posts-related' );
		}
		?>

		<?php
        if ( sq_option( 'post_navigation', 1 ) == 1 ) :
            // Previous/next post navigation.
            kleo_post_nav();
        endif;
		?>
    <!-- Begin Comments -->
     
	<?php if(get_the_author_ID ()!=bbp_get_current_user_id()) :?>
		<a href="#" data-toggle="modal" class="send-report" data-target="#reportsModal" >Report Content</a>
	    <div id="reportsModal" class="modal fade" role="dialog">
		  	<div class="modal-dialog">
		    	<!-- Modal content-->
			    <div class="modal-content">
			      	<div class="modal-header">
			        	<button type="button" class="close" data-dismiss="modal">&times;</button>
			        	<h4 class="modal-title">Leave Report</h4>
			      	</div>
			      	<div class="modal-body">
			      		<?=do_shortcode('[gravityform id=11 title=false description=false ajax=true tabindex=49]')?>
			      	</div>
			      	<div class="modal-footer">
			        	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			      	</div>
			    </div>
			    <!-- Modal end-->
		  	</div>
		</div>
    <?php endif;?>
	<?php get_template_part( 'reports' ); ?>
    <?php //comments_template( '', true ); ?>
    <!-- End Comments -->

<?php endwhile; ?>

<?php get_template_part('page-parts/general-after-wrap');?>

<?php get_footer(); ?>