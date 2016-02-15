<!DOCTYPE html>

<html <?php language_attributes(); ?> >
	
<head>
	
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	
	<title>WP-Quiz - Test your WordPress knowledge and improve your skills</title>
	
	<link href='https://fonts.googleapis.com/css?family=Lato:400,100,100italic,300,300italic,400italic,700,900,700italic,900italic' rel='stylesheet' type='text/css'>
	
	<?php $template_directory_uri = get_template_directory_uri(); ?>
	
	<meta property="og:title" content="WordPress Quiz">
	<meta property="og:site_name" content="WP-Quiz">
	<meta property="og:url" content="http://wp-quiz.com">
	<meta property="og:description" content="Test your WordPress knowledge and improve your skills.">
	<meta property="og:image" content="<?php echo $template_directory_uri; ?>/assets/img/icons/apple-icon-180x180.png" />
	<meta property="og:image:width" content="180" />
	<meta property="og:image:height" content="180" />
	<meta property="og:type" content="website">
	
	<link rel="apple-touch-icon" sizes="57x57" href="<?php echo $template_directory_uri; ?>/assets/img/icons/apple-icon-57x57.png">
	<link rel="apple-touch-icon" sizes="60x60" href="<?php echo $template_directory_uri; ?>/assets/img/icons/apple-icon-60x60.png">
	<link rel="apple-touch-icon" sizes="76x76" href="<?php echo $template_directory_uri; ?>/assets/img/icons/apple-icon-76x76.png">
	<link rel="apple-touch-icon" sizes="120x120" href="<?php echo $template_directory_uri; ?>/assets/img/icons/apple-icon-120x120.png">
	<link rel="apple-touch-icon" sizes="152x152" href="<?php echo $template_directory_uri; ?>/assets/img/icons/apple-icon-152x152.png">
	<link rel="apple-touch-icon" sizes="180x180" href="<?php echo $template_directory_uri; ?>/assets/img/icons/apple-icon-180x180.png">
	<link rel="icon" type="image/png" sizes="192x192"  href="<?php echo $template_directory_uri; ?>/assets/img/icons/android-icon-192x192.png">
	<link rel="icon" type="image/png" sizes="32x32" href="<?php echo $template_directory_uri; ?>/assets/img/icons/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="96x96" href="<?php echo $template_directory_uri; ?>/assets/img/icons/favicon-96x96.png">
	<link rel="icon" type="image/png" sizes="16x16" href="<?php echo $template_directory_uri; ?>/assets/img/icons/favicon-16x16.png">
	
	<?php wp_head(); ?>
	
</head>

<body>
		
	<div class="wrapper">
	
		<header class="site-header">
			
			<div class="content-body">
			
				<div class="title">
					<a href="/" id="logo" class="header-brand">
						<span class="header-brand-logo"><?php bloginfo( 'name' ); ?></span>
						<!--<span class="header-brand-text"></span>-->
					</a>
				</div>
				
				<div class="account">
					
					<?php $current_user_id = get_current_user_id(); ?>
					<?php $current_user = get_userdata( $current_user_id ); ?>
					
					<?php if( $current_user_id ): ?>
						<span class="logged-in"><?php echo $current_user->user_login; ?></span>
						<span class="log-out"><a href="<?php echo wp_logout_url(); ?>" class="a-log-out btn">Log out</a></span>
						
					<?php else: ?>
						<span class="login"><a href="/login" class="a-login btn">Login</a></span>
						<span class="join"><a href="/join" class="a-join btn">Join</a></span>
					<?php endif; ?>
				</div>
			
			</div>
		
		</header>