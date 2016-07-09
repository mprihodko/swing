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

		 
    <?php
		$post_class = 'clearfix';
		if( is_single() && get_cfield( 'centered_text' ) == 1 ) { $post_class .= ' text-center'; }
		?>

		<!-- Begin Article -->
		<article id="post-<?php the_ID(); ?>" <?php post_class(array( $post_class )); ?>>

			<?php if (! is_single() ) : ?>
				<h2 class="article-title entry-title">
					<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'kleo_framework' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark"><?php the_title(); ?></a>
				</h2>
			<?php endif; //! is_single() ?>

			<?php if( kleo_postmeta_enabled() ): ?>
				<div class="article-meta">
					<span class="post-meta">
						<?php kleo_entry_meta();?>
					</span>
					<?php edit_post_link( esc_html__( 'Edit', 'kleo_framework' ), '<span class="edit-link">', '</span>' ); ?>
				</div><!--end article-meta-->
			<?php endif;?>
			
			<?php if ( kleo_postmedia_enabled() && kleo_get_post_thumbnail() != '' ) : ?>

				<div class="article-media">
					<?php echo kleo_get_post_thumbnail( null, 'kleo-full-width' );?>
				</div><!--end article-media-->

			<?php endif; ?>


			<div class="article-content">
			<?php if ( ! is_single() ) : // Only display Excerpts for Search ?>

				<?php echo kleo_excerpt( 50 ); ?>
		        <p class="kleo-continue"><a class="btn btn-default" href="<?php the_permalink()?>"><?php esc_html_e("Continue reading", 'kleo_framework');?></a></p>

			<?php else : ?>
				
			<?php 
		  // get raw date
      $date = get_field('date', false, false);
      // make date object
      $date = new DateTime($date);
      ?>

			<strong>Date:</strong> <?php echo $date->format('j M Y'); ?>

				<?php the_content( esc_html__( 'Continue reading <span class="meta-nav">&rarr;</span>', 'kleo_framework' ) ); ?>
				<?php wp_link_pages( array( 'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'kleo_framework' ), 'after' => '</div>' ) ); ?>

			<?php endif; ?>
			</div><!--end article-content-->

		</article><!--end article-->




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
		        	<h4 class="modal-title">Report Content</h4>
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
    <!-- End Comments -->

<?php endwhile; ?>

<?php get_template_part('page-parts/general-after-wrap');?>

<?php get_footer(); ?>