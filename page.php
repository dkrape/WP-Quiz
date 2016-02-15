<?php
/**
 * Page
 *
 * @package WordPress
 * @subpackage WP Quiz
 * @since WordPress Quiz 1.0
 */
 ?>
 
<?php get_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>

	<article id="post-<?php the_ID(); ?>" <?php post_class( 'content-body' ); ?>>
	
		<header class="entry-header">
			<?php the_title( sprintf( '<h2 class="page-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
		</header><!-- .entry-header -->
	
		<div class="entry-content">
			
			<?php the_content(); ?>
			
		</div><!-- .entry-content -->
		
	</article><!-- #post-## -->

<?php endwhile; ?>

<?php get_footer(); ?>

</html>