<?php
	
if ( ! defined( 'WPINC' ) ) { die; }

if ( ! class_exists( 'WP_Question_Post_Type' ) ) {

	class WP_Question_Post_Type {
     
	    /**
	     * Call when initialised
	     */
	    public function __construct() {
		    
	    	// Register post type
	    	add_action( 'init', array( $this, 'register_question_post_type' ) );
			
			// Rewrite permastructure
			add_action('init', array( $this, 'question_permastruct_rewrite') );
		    
			// Action hooks
			add_action( 'init', array( $this, 'init' ) );
			
	    }
     
	    /**
	     * Functions for init
	     */
	    public function init() {
	    	
	    	// Add scripts
	    	add_action( 'admin_enqueue_scripts', array( $this, 'quiz_post_type_scripts' ) );
	    	
			// Add meta box
		    add_action( 'add_meta_boxes', array( $this, 'register_meta_boxes' ) );
			add_action( 'save_post', array( $this, 'save_answer_meta_boxes' ) );
			
			// Rewrite permalink
			add_filter('post_type_link', array( $this, 'question_permalink_rewrite' ), 1, 3);
		    
	    }
	
		/**
		 * Registers "question" custom post type
		 */
		public function register_question_post_type() {
		    register_post_type( 'question', array(
			    
			    // Labels
		        'labels' => array(
		            'name'               => _x( 'Questions', 'post type general name', 'wp-quiz' ),
		            'singular_name'      => _x( 'Question', 'post type singular name', 'wp-quiz' ),
		            'menu_name'          => _x( 'Questions', 'admin menu', 'wp-quiz' ),
		            'name_admin_bar'     => _x( 'Question', 'add new on admin bar', 'wp-quiz' ),
		            'add_new'            => _x( 'Add New', 'question', 'wp-quiz' ),
		            'add_new_item'       => __( 'Add New Question', 'wp-quiz' ),
		            'new_item'           => __( 'New Question', 'wp-quiz' ),
		            'edit_item'          => __( 'Edit Question', 'wp-quiz' ),
		            'view_item'          => __( 'View Question', 'wp-quiz' ),
		            'all_items'          => __( 'All Questions', 'wp-quiz' ),
		            'search_items'       => __( 'Search Questions', 'wp-quiz' ),
		            'parent_item_colon'  => __( 'Parent Questions:', 'wp-quiz' ),
		            'not_found'          => __( 'No questions found.', 'wp-quiz' ),
		            'not_found_in_trash' => __( 'No questions found in Trash.', 'wp-quiz' ),
		        ),
		         
		        // Frontend
		        'has_archive'        => false,
		        'public'             => true,
		        'publicly_queryable' => true,
		        'rewrite'			 => array( 'slug', 'question' ),
		         
		        // Admin
		        'capability_type'    => 'post',
		        'menu_icon'          => 'dashicons-welcome-learn-more',
		        'menu_position' 	 => 4,
		        'taxonomies'         => array( 'category', 'post_tag' ),
		        'query_var'          => true,
		        'show_in_menu'       => true,
		        'show_ui'            => true,
		        'supports'           => array(
		            'editor',
		            'title'
		        ),
		        
		        // Rest
		        'show_in_rest'       => true,
		        'rest_base'          => 'question',
		        
		    ) );    
		}
	
		/**
		* Enqueues scripts
		*/
		public function quiz_post_type_scripts() {
			wp_enqueue_style( 'quiz_post_type', get_template_directory_uri() . '/assets/css/admin/quiz_post_type.css' );
			wp_enqueue_script( 'admin_quiz_post_type', get_template_directory_uri() . '/assets/js/admin/quiz_post_type.js', array( 'jquery' ), '1.0.0', true );
		}
	
		/**
		* Registers a Meta Box for Questions custom post type, called 'Answers'
		*/
		public function register_meta_boxes() {
			add_meta_box( 'answers', 'Answers', array( $this, 'answers_meta_box' ), 'question', 'normal', 'high' );
		}
		
		/**
		* Output of answers meta box
		*/
		public function answers_meta_box( $post ) {
			
			// Get answers
			$answers = get_post_meta( $post->ID, '_answers', true );
			$answers = json_decode( $answers );
			
			// Get correct answer
			$answer_correct = get_post_meta( $post->ID, '_answer_correct', true );
			
			// Get more information field
			$answer_more_info = get_post_meta( $post->ID, '_answer_more_info', true );
			
			// Answers: Answer list
			
			echo '<div class="answers">';
			
			echo '<p><strong>Answer choices:</strong></p>';
			
			if( !$answers || !is_array( $answers ) ) {
				$answers = array_fill(0,2,'');
			}
			
			$i = 0;
			
			foreach( $answers as $answer ) {
				
				echo ( '<div class="single-answer">' );
				
				// Answer field
				echo ( '<label for="answer_' . $i . '">' . __( 'Answer', 'question' ) . '</label>'  );
			    echo ( '<textarea type="text" name="answer[]" id="answer_' . $i . '" class="answer-field" tabindex="' . ( $i + 1 ) . '" rows="2">' . esc_attr( htmlentities( $answer ) ) . '</textarea>'  );
				
				// Correct answer
				$checked = ( $i == $answer_correct ? 'checked="checked"' : '' );
			    echo ( '<span class="answer-correct-wrapper"><input type="radio" name="answer_correct" value="' . esc_attr( $i ) . '" class="answer-correct"  ' . $checked . ' /></span>'  );
			    
			    // Remove answer
			    echo ( '<a href="#" class="remove-answer button button-secondary">x</a>' );
			    echo ( '</div>' );
			    
			    $i++;
				
			}
			
			echo '</div>';
		    
			echo '<input type="button" value="+ Add answer" class="add-answer button button-secondary">';
		    
		    // Answers: More information
		    
		    echo '<hr class="answer-section-hr" />';
			
			echo '<p><strong>More about the answer:</strong></p>';
			
		    echo '<textarea type="text" name="answer_more_info" id="answer_more_info" class="answer-field" rows="6">' . esc_attr( htmlentities( $answer_more_info ) ) . '</textarea>' ;
		    
		    // Answers: More information: Quick add
		    
		    echo '
		    
		    <div class="handy-text">
		    
		    	<p><strong>Handy text</strong></p>
		    	
		    	<ul>
		    		<li>&lt;p&gt;&lt;a href="https://codex.wordpress.org/Function_Reference/FUNCTION"&gt;View &lt;code&gt;FUNCTION()&lt;/code&gt; in the WordPress Codex&lt;/a&gt;&lt;/p&gt;</li>
		    	</ul>
		    	
			</div>';
			
			// Add a nonce field
		    wp_nonce_field( 'save_answers', 'answers_nonce' );
		    
		}
		
		/**
		* Saves the meta box field data
		*
		* @param int $post_id Post ID
		*/
		public function save_answer_meta_boxes( $post_id ) {
			
			// Check if nonce is set
			if ( ! isset( $_POST['answers_nonce'] ) ) {
			    return $post_id;    
			}
			
			// Verify that nonce is valid
			if ( ! wp_verify_nonce( $_POST['answers_nonce'], 'save_answers' ) ) {
			    return $post_id;
			}
		 
		    // Check this is the Question custom post type
		    if ( 'question' != $_POST['post_type'] ) {
		        return $post_id;
		    }
		 
		    // Check the logged in user has permission to edit this post
		    if ( ! current_user_can( 'edit_post', $post_id ) ) {
		        return $post_id;
		    }
		    
		    // Gets and parses answers
		    $answers = $_POST['answer'];
		    
		    foreach( $answers as $answer ) {
			    
			    $answer = str_replace( ["\r\n", "\r", "\n"], "<br/>", $answer );
			    
				$answers_parsed[] = wp_kses( $answer , array( 
				    'a' => array(
				        'href' => array(),
				        'title' => array()
				    ),
				    'pre' => array(),
				    'code' => array(),
				    'strong' => array(),
				    'em' => array(),
				    'br' => array()
				) );
			
		    }
		    
		    $answers_parsed = json_encode( $answers_parsed );
		    
		    //echo $answers_parsed;
		    
		    // Gets the correct answer
		    $answer_correct = sanitize_text_field( $_POST['answer_correct'] );
		    
		    // Gets the correct answer
		    $answer_more_info = wp_kses_post( $_POST['answer_more_info'] );
		    		    
		    // Save metadata
		    update_post_meta( $post_id, '_answers', $answers_parsed );
		    update_post_meta( $post_id, '_answer_correct', $answer_correct );
		    update_post_meta( $post_id, '_answer_more_info', $answer_more_info );
		     
		}
		
		/**
		* Question permalink rewrite
		*
		* @reference https://wordpress.org/support/topic/custom-post-type-permalink-structure
		*/

		public function question_permastruct_rewrite() {
			global $wp_rewrite;

			$queryarg = 'post_type=question&p=';
			$wp_rewrite->add_rewrite_tag( '%question%', '([^/]+)', $queryarg );
			$wp_rewrite->add_permastruct( 'question', '/question/%question%/', false );
		}
		
		/**
		* Question link url permalink rewrite
		*
		* @param string $post_link
		* @param id $id Post ID
		* @param string $leavename
		*
		* return string $newlink New link with ID 
		*/
		
		public function question_permalink_rewrite( $post_link, $id = 0, $leavename ) {
			global $wp_rewrite;
		
			$post = &get_post($id);
		
			if ( is_wp_error( $post ) )
				return $post;
		
			$newlink = $wp_rewrite->get_extra_permastruct( 'question' );
			$newlink = str_replace( '%question%' , $post->ID, $newlink );
			$newlink = home_url( user_trailingslashit( $newlink ) );
		
			return $newlink;
		}
		
	}
	
}
 
$WP_Question_Post_Type = new WP_Question_Post_Type;