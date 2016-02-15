<?php
/**
 * Template Name: Account - Sign up
 *
 * @package WordPress
 * @subpackage WP Quiz
 * @since WP Quiz 1.0
 * @source http://code.tutsplus.com/tutorials/creating-a-custom-wordpress-registration-form-plugin--cms-20968
 */
	
get_header();
 
?>
 
<div class="content-body page-auth page-signup">
	
<?php echo $custom_quiz_auth->render_form(); ?>

</div>
