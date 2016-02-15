<?php
	
if ( ! defined ( 'WPINC' ) ) { die; }

show_admin_bar( false );

/**
 * Custom quiz post type
 *
 * @since WP Quiz 1.0
 */
require get_template_directory() . '/inc/quiz-post-type.php';

/**
 * REST API Integration
 *
 * @since WP Quiz 1.0
 */
require get_template_directory() . '/inc/theme/question-rest.php';

/**
 * Authentication functions
 *
 * @since WP Quiz 1.0
 */
require get_template_directory() . '/inc/theme/custom-auth.php';


function change_author_permalinks() {

    global $wp_rewrite;

    // Change the value of the author permalink base to whatever you want here
    $wp_rewrite->author_base = 'user';
}

add_action('init','change_author_permalinks');