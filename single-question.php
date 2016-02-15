<?php
/**
 * Index page
 *
 * @package WordPress
 * @subpackage WP Quiz
 * @since WordPress Quiz 1.0
 */
?>
 
<?php get_header(); ?>

<div id="question-box" class="loading content-body question-box" aria-live="assertive">
	<!-- View data -->
</div>
		
<?php get_footer(); ?>

<?php
	//Include question template
	require_once dirname( __FILE__ ) . '/inc/theme/template-question.html';	
?>

<input type="hidden" id="previous-questions-log" value="" />

</html>