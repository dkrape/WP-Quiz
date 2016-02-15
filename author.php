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

<?php
	
$curr_user = (isset($_GET['author_name'])) ? get_user_by('slug', $author_name) : get_userdata(intval($author));
	
global $wpdb;

$sql = 'SELECT COUNT(*) FROM WP_2_QUESTION_ANSWERS WHERE ANSWER_CORRECT = 1 AND USER_ID = ' . $curr_user->id ;
$answers_correct = $wpdb->get_var( $sql );

$sql = 'SELECT COUNT(*) FROM WP_2_QUESTION_ANSWERS WHERE ANSWER_CORRECT = 0 AND USER_ID = ' . $curr_user->id ;
$answers_incorrect = $wpdb->get_var( $sql );

?>

<h2><?php echo $curr_user->user_login; ?></h2>

<p><strong>Correct:</strong> <?php echo $answers_correct; ?></p>
<p><strong>Incorrect:</strong> <?php echo $answers_incorrect; ?></p>

<?php get_footer(); ?>

</html>