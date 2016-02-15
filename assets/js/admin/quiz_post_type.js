jQuery(document).ready(function() {
	
	jQuery(".add-answer").live("click", function() {
	
		var fieldCount = jQuery( ".answers input" ).length + 1;
		
		var field = "<div class='single-answer'>";
		field += "<label for='answer_" + fieldCount + "'>Answer</label>";
		field += "<textarea type='text' name='answer[]' id='answer_" + fieldCount + "' class='answer-field' value='' rows='2'></textarea>";
		field += "<span class='answer-correct-wrapper'><input type='radio' name='answer_correct' value='' class='answer-correct' /></span>";
		field += "<a href='#' class='remove-answer button button-secondary' ref='" + fieldCount + "'>x</a>";
		field += "</div>";
		
		jQuery(".answers").append( field );
		
		reIndexFields();
		
		return false;
		
	});
	
	jQuery(".remove-answer").live("click", function() {
		
		jQuery(this).parent().remove();
		
		reIndexFields();
		
		return false;
		
	});
	
	var reIndexFields = function() {
		
		jQuery( ".answers .single-answer" ).each( function( i ) {
			
			jQuery(this).find(".answer-correct").attr( "value", i );
			
			i++;
				
		});
		
	};
	
});