<?php
/**
 * Custom sign-up and authentication
 *
 * @package WordPress
 * @subpackage WP Quiz
 * @since WordPress Quiz 1.0
 * @reference http://www.paulund.co.uk/create-your-own-wordpress-login-page
 */
	
if ( !defined( 'WPINC' ) ) { die; }

if ( ! class_exists( 'Quiz_Custom_Auth' ) ) {
	
	Class Quiz_Custom_Auth {
		
		/**
	     * Call when initialised
	     */
		public function __construct() {
			
			if( $_POST && isset( $_POST['quiz_custom_registration'] ) ) {
				$this->post_registration();
			}
			
			add_action( 'wp_login_failed', array( $this, 'login_failed' ) );
			add_action( 'authenticate', array( $this, 'blank_login' ) );
			
		}
		
		/**
		 * Action for login form, redirect with error query string
		 *
		 * @since WP Quiz 1.0
		 */
		public function render_form() {

			?>
		
			<form action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="post">
				
				<?php echo $this->render_form_errors( 'username' ); ?>
		
				<p class="auth-set">
					<label class="auth-label auth-label-username" for="username">Username</label>
					<input class="auth-input auth-input-username" type="text" name="username" value="<?php echo ( isset( $_POST['username'] ) ? esc_attr( $_POST['username'] ) : null ) ?>" />
					<span class="auth-help">Lower case only</span>
				</p>
				
				<?php echo $this->render_form_errors( 'email' ); ?>
				
				<p class="auth-set">
					<label class="auth-label auth-label-email" for="email">Email</label>
					<input class="auth-input auth-input-email" type="text" name="email" value="<?php echo ( isset( $_POST['email']) ? esc_attr( $_POST['email'] ) : null ) ?>" />
				</p>
				
				<?php echo $this->render_form_errors( 'password' ); ?>
				
				<p class="auth-set">
					<label class="auth-label auth-label-password"  for="password">Password</label>
					<input class="auth-input auth-input-password" type="password" name="password" value="<?php echo ( isset( $_POST['password'] ) ? esc_attr( $_POST['password'] ) : null ) ?>" />
				</p>
				
				<input type="hidden" name="signup_register_nonce" value="<?php echo wp_create_nonce('signup-register-nonce'); ?>" />
				<input type="hidden" name="quiz_custom_registration" value="1" />
				
				<input class="btn btn-primary" type="submit" name="submit" value="Register"/>
		
			</form>
			
			<?php
			
		}
		
		/**
		 * Parses individual error messages
		 *
		 * @since WP Quiz 1.0
		 */
		public function render_form_errors( $type ) {

			global $reg_errors;
			
			$error_msg = "<div class='msg msg-error'>%s</div>";
			
			$error_output = false;
					
			if( $reg_errors->errors[ $type ] ) {
				
				$errors_array = $reg_errors->errors[ $type ];
				
				foreach( $errors_array as $error_text ) {
					
					$errors[] = sprintf( $error_msg, $error_text );
				
				}
				
				$error_output = implode( $errors );
			}
			
			return $error_output;
			
		}
		
		/**
		 * On post validate
		 *
		 * @since WP Quiz 1.0
		 */
		public function post_registration() {
			    
	        $this->registration_validation(
		        $_POST['username'],
		        $_POST['password'],
		        $_POST['email']
	        );
	         
	        // Sanitize user form input
	        global $username, $password, $email;
	        
	        $username   =   sanitize_user( $_POST['username'] );
	        $password   =   esc_attr( $_POST['password'] );
	        $email      =   sanitize_email( $_POST['email'] );
	 
	        // Call @function complete_registration to create the user
	        // Only when no WP_error is found
	        $this->submit_registration( $username, $password, $email );
		
		}
		
		/**
		 * Form validation
		 *
		 * @since WP Quiz 1.0
		 */
		public function registration_validation( $username, $password, $email )  {
			
			global $reg_errors;
			$reg_errors = new WP_Error;
			
			$password_length = 4;
			
			if ( username_exists( $username ) ) {
				$reg_errors->add('username', 'That username already exists');
			}
			
			if ( ! validate_username( $username ) ) {
				$reg_errors->add( 'username', 'The username you entered is not valid' );
			}
			
			if ( $password_length > strlen( $password ) ) {
				$reg_errors->add( 'password', 'Password length must be greater than ' . $password_length . ' characters' );
			}
			
			if ( !is_email( $email ) ) {
				$reg_errors->add( 'email', 'Email is not valid' );
			}
			
			if ( email_exists( $email ) ) {
				$reg_errors->add( 'email', 'Email is already in use' );
			}
			
		}
		
		/**
		 * When no errors, submit registation, log in and redirect
		 *
		 * @since WP Quiz 1.0
		 */
		public function submit_registration() {
			
			global $reg_errors, $username, $password, $email;
			
			if ( 1 > count( $reg_errors->get_error_messages() ) ) {
				
				$userdata = array(
					'user_login'    =>   $username,
					'user_email'    =>   $email,
					'user_pass'     =>   $password,
				);
				
				$user = wp_insert_user( $userdata );
		
				if( $user ) {
				
					$credentials = array(
						'user_login' => $username,
						'user_password' => $password,
						'remember' => true,
					);
					
					$user = wp_signon( $credentials, false );
					
					if ( is_wp_error( $user ) ) {
						echo $user->get_error_message();
					} else {
						
						//Redirect to next question
						header( 'Location: http://' . $_SERVER['HTTP_HOST'] );
					}
				
				}
		
			}
			
		}
		
		/**
		 * Add action for login failed, redirect with error query string
		 *
		 * @since WP Quiz 1.0
		 */
		public function login_failed( $user ) {
			
			$referrer = $_SERVER['HTTP_REFERER'];
			
			// Check that not on default login page
			if ( !empty($referrer) && !strstr($referrer,'wp-login') && !strstr($referrer,'wp-admin') && $user!=null ) {
			
				// Check if existing failed login
				if ( !strstr($referrer, '?login=failed' )) {
					// Redirect to login page and add failed query string
					wp_redirect( $referrer . '?login=failed');
				} else {
					wp_redirect( $referrer );
				}
				
				exit;
			}
		}
				
		/**
		 * Add action for login blank, redirect with error query string
		 *
		 * @since WP Quiz 1.0
		 */
		public function blank_login( $user ){
			
		  	$referrer = $_SERVER['HTTP_REFERER'];
		  	$request_uri = $_SERVER['REQUEST_URI'];
		
		  	$error = false;
		
		  	if($_POST['log'] == '' || $_POST['pwd'] == '') {
				$error = true;
		  	}
		
		  	// Check that not on default login page
		  	if ( !empty($referrer) && !strstr($referrer,'wp-login') && !strstr($referrer,'wp-admin') && $error ) {
		
				// Check if existing failed login or from logout
				if ( !strstr($referrer, '?login=failed') && !strstr($request_uri, '?loggedout=true') ) {
					// Redirect to login page and add failed query string
					wp_redirect( $referrer . '?login=failed' );
				} else {
					wp_redirect( $referrer );
				}
		
				exit;
		
			}
		}

	}	
	
}

$custom_quiz_auth = new Quiz_Custom_Auth();