var Question = window.Question || {};

Question = (function() {

	var config = {
		settings: {
			apiUrl: window.location.protocol + '//' + window.location.hostname + '/wp-json/wp/v2/',
			siteUrl: window.location.protocol + '//' + window.location.hostname + '/question/',
			questionLogCount: 8
		},
		el: {
			logo:				'#logo',
			loading:			'.loading',
			questionBox:		'#question-box',
			questionTemplate:	'#question-template',
			questionForm:		'#question-form',
			questionAnswerLabel: '.question-answer-label',
			checkAnswerBtn:		'#question-check-answer-btn',
			nextQuestionBtn:	'#question-next-btn',
			questionMessage:	'#question-message',
			questionMoreInfo:	'#question-more-info',
			previousQuestionsLog: '#previous-questions-log',
		},
		strings: {
			incorrectAnswer: 'Whoops, incorrect',
			correctAnswer: 'That\'s correct!',
			chooseAnswer: 'Please choose an answer'
		},
	};
	
	var func = {
		
		init: function () {
			
			window.console.log( 'question init' );
			
			//Load question, get ID from URL
			var currentUrl = window.location.pathname.split( '/' );
			func.loadQuestion( currentUrl[ 2 ] );
			
			//Set click states 
			jQuery( config.el.checkAnswerBtn ).live( "click", func.checkAnswer );
			jQuery( config.el.nextQuestionBtn ).live( "click", func.nextQuestion );
			
		},
		
		// Get random question
		randomQuestion: function() {
			
		},
		
		// Loads question data, populates template, and adds to page
		loadQuestion: function( questionId ) {
		
			window.console.log( 'loadQuestion: ' + questionId );
				
			// Load question data	
			jQuery.get( config.settings.apiUrl + 'question/' + questionId, function( data ) {
				
				// Remove loading
				jQuery( config.el.loading ).removeClass( 'loading' );
				
				data._answers = JSON.parse( data._answers[0] );
	
				// Add data to template
				var output = { data: data },
					template = _.template( jQuery( config.el.questionTemplate ).html(), output );
					
				jQuery( config.el.questionBox ).html( template );
				
				// Handle buttons
				jQuery( config.el.checkAnswerBtn ).show();
				jQuery( config.el.nextQuestionBtn ).hide();
	
			});
				
		},
		
		// Check answer
		checkAnswer: function( e ) {
		
			window.console.log( 'checkAnswer' );
			
			e.preventDefault();
			
			// Get the current question id
			var questionId = jQuery( config.el.questionForm ).attr('ref');
			
			// Get the selected answer
			var chosenAnswer = jQuery( config.el.questionForm + ' input[name=answer]:checked' ).val();
			
			if( !chosenAnswer ) {
				
				jQuery( config.el.questionMessage ).removeClass('correct').addClass( 'incorrect' ).html( config.strings.chooseAnswer );
				
			} else {
			
				// Get previous questions
				var previousQuestions = jQuery( config.el.previousQuestionsLog ).val();
								
				// Get the question answer data
				jQuery.ajax( {
				    url: config.settings.apiUrl + 'question/answer/' + questionId,
				    data: {
					    'chosenAnswer': chosenAnswer,
					    'previousQuestions': previousQuestions + ',' + questionId
					},
				    method: 'POST',
				    beforeSend: function ( xhr ) {
				        xhr.setRequestHeader( 'X-WP-Nonce', question_object.nonce );
				    }
				} )
				.done( function ( questionReturn ) {
				
					// Handle if the chosen answer is correct or not
					if( chosenAnswer === questionReturn.answerCorrect ) {
						jQuery( config.el.questionMessage ).removeClass('incorrect').addClass( 'correct' ).html( config.strings.correctAnswer );
					} else {
						jQuery( config.el.questionMessage ).removeClass('correct').addClass( 'incorrect' ).html( config.strings.incorrectAnswer );
					}
					
					// More information about the answer
					if( questionReturn.answerMoreInfo ) {
						
						var moreInfo = JSON.parse( questionReturn.answerMoreInfo );
						
						jQuery( config.el.questionMoreInfo ).find( '.text' ).html( moreInfo );
						jQuery( config.el.questionMoreInfo ).slideDown();
					
					} else {
						
						jQuery( config.el.questionMoreInfo ).hide();
						jQuery( config.el.questionMoreInfo ).find( '.text' ).html();
					
					}
					
					// Sets the answer correct/incorrect display
					jQuery( config.el.questionAnswerLabel ).addClass( 'question-answer-label-incorrect' );
					jQuery( config.el.questionAnswerLabel + ':eq( ' + questionReturn.answerCorrect +  ' )' ).removeClass( 'question-answer-label-incorrect' ).addClass( 'question-answer-label-correct' );
					
					// Handle buttons
					jQuery( config.el.checkAnswerBtn ).hide();
					jQuery( config.el.nextQuestionBtn ).attr( 'ref', questionReturn.nextQuestion ).show();
				
					// Log previous questions
					func.logPreviousQuestions( questionId, previousQuestions );
				
				} )
				.error( function( ) {
					window.console.log( 'Failed. Kaboom. Kablooie. For some reason. Sorry.' );
				} );
	
			}
			
		},
		
		// Next question
		nextQuestion: function( e ) {
			
			window.console.log( 'nextQuestion' );
			
			e.preventDefault();
			
			var questionId = jQuery( this ).attr("ref");
			
			var newUrlPath = '/question/' + questionId;
			
			window.history.pushState( { 'questionId': questionId } , '' , newUrlPath );
			
			func.loadQuestion( questionId );
			
		},
		
		// Log previous questions (so they don't repeat)
		logPreviousQuestions: function( questionId, previousQuestions ) {
			
			window.console.log( 'logPreviousQuestions' );
			
			if( previousQuestions ) {
				
				previousQuestions = previousQuestions.split( "," );
				
				previousQuestions.push( questionId );
				
				previousQuestions = previousQuestions.slice( -config.settings.questionLogCount );
				
			} else  {
				
				previousQuestions = questionId;
			}
			
			jQuery( config.el.previousQuestionsLog ).val( previousQuestions );
			
		}
	};
	
	var api = {
		init: func.init
	};
	
	return api;
	
})();

//Run
( function() {
	
	// If URL matches question
	
	if(window.location.href.indexOf( '/question/' ) > -1) {
	    Question.init();
    }
	
})( jQuery, _ );
