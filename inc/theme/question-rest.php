<?php

/**
 * Version number for our theme.
 *
 * @var string
 */
define( 'QUESTION_APP_VERSION', '2.0' );

/**
 * File assets URI
 *
 * @var string
 */
define( 'QUESTION_APP_ASSET_URI', esc_url( get_template_directory_uri() . '/assets/' ) );
	
/*
 *
 *
 */

class RESTful_Question_Class extends WP_REST_Controller {
	
	/**
	 * The one instance of RESTful Question route
	 *
	 * @var RESTful Question Output
	 */
	 
	private static $instance;
	
	/**
	 * Instantiate or return the one RESTful Question route instance
	 *
	 * @return RESTful Question Output
	 */
	 
	public static function instance() {
		
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		
		return self::$instance;
	}
	
	/**
	 * Construct the object.
	 *
	 * @return RESTful Question Output
	 */
	
	public function __construct() {
		
		// When on root, redirect to question
		add_action( 'send_headers', array( $this, 'root_redirect_to_question' ) );
		
		// Enqueue scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );
		
		// Register custom endpoints
		add_action( 'rest_api_init', array( $this, 'register_process_answer_route' ) );
		
		add_action( 'rest_api_init', array( $this, 'register_answers_field' ) );
		
		// Unprotect answer custom meta
		add_filter( 'is_protected_meta', array( $this, 'unprotect_answers_field' ), 10, 2 );
	}
	
	/*
	 *
	 *
	 */
	
	public function root_redirect_to_question() {
		
		global $current_blog;
		
        if( $_SERVER['REQUEST_URI'] == '/' ) {
	        
			$question_id = $this->get_next_question();
			
			header( 'LOCATION: http://' . $current_blog->domain . '/question/' . $question_id );
	        
        }
		
	}
	
	/*
	 *
	 *
	 */
	
	public function register_scripts() {
		
		wp_enqueue_style( 'question-styles', QUESTION_APP_ASSET_URI . 'css/style.css' );
		
		wp_enqueue_script( 'question-main', QUESTION_APP_ASSET_URI . 'js/theme/question-rest.js',
			array( 'jquery', 'underscore' ), QUESTION_APP_VERSION, true );
			
		wp_localize_script(
			'question-main',
			'question_object',
			array(
				'ajax_url' => esc_url_raw( rest_url() ),
				'nonce' => wp_create_nonce( 'wp_rest' )
			)
			
		);
		
	}
	
	/*
	 *
	 * Reference: http://v2.wp-api.org/extending/adding/
	 */
	
	public function register_answers_field() {
		
		register_rest_field( 'question',
			'_answers',
			array(
				'get_callback'    => array( $this, 'get_answers_cb' ),
	            'update_callback' => null,
	            'schema'          => null
			)
		);
	
	}
	
	/*
	 * When question is pulled, also get answers
	 *
	 */
	
	public function get_answers_cb( $object, $field_name, $request ) {
		
		return get_post_meta( $object[ 'id' ], $field_name );
	
	}

	/*
	 * ...
	 *
	 */
	 	
	public function unprotect_answers_field( $protected, $meta_key ) {
		if( '_answers' == $meta_key || '_answers' == $meta_key && defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			$protected = false;
		}
		
		return $protected;
	}
	
	/*
	 * Registers the routes for the objects of the controller
	 */
	
	public function register_process_answer_route() {
		
		$version = '2';
		$namespace = 'wp/v' . $version;
		$base = 'question/answer';
		
		register_rest_route( $namespace, '/' . $base . '/(?P<id>\d+)', array(
				'methods'		=> WP_REST_Server::CREATABLE,
				'callback'		=> array( $this, 'process_correct_answer' ),
				'args' 			=> array(),
			)
		);
		
	}
	
	/**
	 * Processes the correct answer
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|WP_REST_Response
	 */
   
	public function process_correct_answer( $request ) {
		
		$params = $request->get_params();
		
		// Set variables
		$question_id = (int) $request[ 'id' ];
		$question_chosen_answer = (int) $params['chosenAnswer'];
		
		//Get previous questions
		$previous_questions = $params['previousQuestions'];
		if( $previous_questions ) {
			$previous_questions = explode(',' , $previous_questions );
		} else {
			$previous_questions = $question_id;
		}
		
		$question_answer_correct =  get_post_meta( $question_id, '_answer_correct', true );
		
		// Do we have a selected answer?
		if( isset( $question_answer_correct ) ) {
			
			$current_user = wp_get_current_user();
		
			// Was the chosen answer correct?
			if( $question_answer_correct == $question_chosen_answer ) { 
				$is_correct = 1;
			} else {
				$is_correct = 0;
			}
			
			/* Insert record into DB */
			
			global $wpdb;
	
			$wp_insert = $wpdb->insert( 
				$wpdb->prefix . 'question_answers', 
				array( 
					'question_id'		=> $question_id,
					'answer_chosen'		=> $question_chosen_answer,
					'answer_correct'	=> $is_correct,
					'answer_date'		=> current_time('mysql', 1),
					'user_id'			=> $current_user->ID
				)
			);
			
			// Get the next question
			$next_question = $this->get_next_question( $previous_questions );
			
			$return_arr = array(
				'answerCorrect'			=> $question_answer_correct,
				'nextQuestion'			=> $next_question,
				'user_id'				=> $current_user->ID
			);
			
			// More information about the answer
			$answer_more_info = get_post_meta( $question_id, '_answer_more_info', true );
			
			if( $answer_more_info ) {
				$return_arr[ 'answerMoreInfo' ] = json_encode( $answer_more_info );
			}
		
			return new WP_REST_Response( $return_arr, 200 );
		} else {
			return new WP_Error( 'code', __( 'message', 'text-domain' ) );
		}
		
	}
	
	/**
	 * Gets the next question
	 *
	 * @param $id int
	 * @return $next_question int
	 */
	
	public function get_next_question( $previous_questions = null ) {
		
		$args = array(
			'post_type'			=> 'question',
			'posts_per_page'	=> 1,
			'orderby'			=> 'rand',
			'exclude'			=> $previous_questions
			);
			
		$next_question = get_posts( $args );
		
		return $next_question[0]->ID;
		
	}
	
}

/**
 * Wrapper function to return the one RESTful Question instance.
 * @return    RESTful Question Output
 */
function RESTful_Question() {
	
	return RESTful_Question_Class::instance();

}

// Kick off the class.
add_action( 'init', 'RESTful_Question' );