<?php
/**
 * Template Name: Account - Log in
 *
 * @package WordPress
 * @subpackage WP Quiz
 * @since WP Quiz 1.0
 * @source http://www.paulund.co.uk/create-your-own-wordpress-login-page
 */
	
	get_header();
 
 ?>
 
<div class="content-body page-auth page-login">
 
<?php

	if(isset($_GET['login']) && $_GET['login'] == 'failed')
	{
		?>
			<div id="login-error">
				<p>Login failed: You have entered an incorrect Username or password, please try again.</p>
			</div>
		<?php
	}

	$args = array(
		'echo'           => true,
		'remember'       => true,
		'redirect'       => ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'],
		'value_username' => '',
		'value_remember' => true
	);
	
	global $user_login;
	
	if (is_user_logged_in()) {
	    echo 'You are currently logged in. <a href="', wp_logout_url(), '" title="Logout">Logout</a>';
	} else {
	    wp_login_form( $args );
	}

?>

</div>
