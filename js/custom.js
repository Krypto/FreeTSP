/*!
 * Switch Form v1.0
 * 
 * Copyright 2011 - Gnt Studio
 * info@gntstudio.eu
 * http://www.gntstudio.eu
 */
$(document).ready(function() {
	
	//HIDE LOADING
	$('.loading').hide();
	
	//DEFINE VARIABLES
	var $box			= $('.box');
	var $active_form 	= $box.children('form.active');
	
	//DISPLAYS ONLY THE ACTIVE FORM	
	$box.children('form').each(function() {

		if (!$(this).hasClass('active'))
			$(this).hide();
		
	});
	
	//LINK FOR CHANGE FORM
	$('.link').bind('click',function(e) {
		
		var target = $(this).attr('rel');
		$('.loading').show();
		
		$active_form.fadeOut(400, function() {

			$active_form.removeClass('active');
			$active_form = $box.children('form#' + target);
			
			$active_form.addClass('active');
			$active_form.fadeIn(400);
			$('.loading').hide();

		});
		
		e.preventDefault();
		
	});
				
});
