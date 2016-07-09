<?php
/**
 * Template Name: Parties
 *
 * Reports main page
 * @package WordPress
 */

get_header(); ?>

<?php
//Specific class for post listing */
$blog_type = sq_option('blog_type','masonry');
$blog_type = apply_filters( 'kleo_blog_type', $blog_type );

$template_classes = $blog_type . '-listing';
if ( sq_option( 'blog_archive_meta', 1 ) == 1 ) {
    $template_classes .= ' with-meta';
} else {
    $template_classes .= ' no-meta';
}

if ( $blog_type == 'standard' && sq_option('blog_standard_meta', 'left' ) == 'inline' ) {
    $template_classes .= ' inline-meta';
}
add_filter('kleo_main_template_classes', create_function('$cls','$cls .=" posts-listing ' . $template_classes . '"; return $cls;'));
?>

<?php get_template_part('page-parts/general-title-section'); ?>

<?php get_template_part('page-parts/general-before-wrap'); ?> 

<?php if ( category_description() ) : ?>
    <div class="archive-description"><?php echo category_description(); ?></div>
<?php endif; ?>

<?php  wp_reset_postdata(); the_content(); ?>

<?php
$currentdate = date("Y-m-d",mktime(0,0,0,date("m"),date("d")-1,date("Y")));

$posts = get_posts(array(
	'posts_per_page'	=> -1,
	'post_type'			=> 'parties',
	'meta_query' => array(
                      array(
                        'key'     => 'party_date',
                        'compare' => '>',
	                      'value' => $currentdate,
	                      'type' => 'DATE',                        
                      ),
                    ), 
	'meta_key' => 'party_date',
	'orderby' => 'meta_value_num',	
	'order' => 'ASC',
));
?>
<?php if( $posts ): ?>

    <?php if (sq_option('blog_switch_layout', 0) == 1 ) : /* Blog Layout Switcher */ ?>

        <?php kleo_view_switch( sq_option( 'blog_enabled_layouts' ), $blog_type ); ?>

    <?php endif; ?>

    <?php
    if ($blog_type == 'masonry') {
        echo '<div class="row responsive-cols kleo-masonry per-row-' . sq_option( 'blog_columns', 3 ) . '">';
    }
    ?>

    <?php  
    // Start the Loop.
    foreach( $posts as $post ): 
        
        setup_postdata( $post );

        /*
         * Include the post format-specific template for the content. If you want to
         * use this in a child theme, then include a file called called content-___.php
         * (where ___ is the post format) and that will be used instead.
         */

        if ($blog_type != 'standard') :
        		?>
        		<article id="post-<?php the_ID(); ?>" <?php post_class(array("post-item")); ?>>
							<div class="post-content animated animate-when-almost-visible el-appear">
								<div class="post-header">
									
									<?php if ($kleo_post_format != 'status'): ?>
									<h3 class="post-title entry-title"><a href="<?php the_permalink();?>"><?php the_title();?></a></h3>
									<?php endif; ?>

						                <span class="post-meta">
						                    <?php kleo_entry_meta();?>
						                </span>

								</div><!--end post-header-->
								
								<?php if ( $kleo_post_format != 'status' ): ?>
								
									<?php if (kleo_excerpt() != '<p></p>') : ?>
									<div class="post-info">

										<span>
                                            <?php // get raw date
                                                $date = get_field('party_date', false, false);
                                                // make date object
                                                $date = new DateTime($date);
                                                ?>

											<strong>Party Date:</strong> <?php echo $date->format('j M Y'); ?>
										</span>

										<div class="entry-summary">
											<?php echo kleo_excerpt(); ?>
										</div><!-- .entry-summary -->


									</div><!--end post-info-->
									<?php endif; ?>
									
								<?php endif; ?>							
							
								
								<div class="post-footer">
									<small>
										<?php do_action('kleo_post_footer');?>

										<a href="<?php the_permalink();?>"><span class="muted pull-right"><?php esc_html_e( "Read more","kleo_framework" );?></span></a>
									</small>
								</div><!--end post-footer-->

							</div><!--end post-content-->
							</article>

						<?php
           // get_template_part( 'page-parts/post-content-' . $blog_type );

        else:
            $post_format = kleo_get_post_format();
          	
            get_template_part( 'content', $post_format );
        endif;

    endforeach;
    ?>

    <?php
    if ($blog_type == 'masonry') {
        echo '</div>';
    }
    ?>

    <?php
    // page navigation.
    kleo_pagination();

else :
    // If no content, include the "No posts found" template.
    get_template_part( 'content', 'none' );

endif;
?>

<?php get_template_part('page-parts/general-after-wrap'); ?>

<?php get_footer(); ?>