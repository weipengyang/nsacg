$(function($) {
	$.fn.jfade = function(settings) {
   
	var defaults = {
		start_opacity: "1",
		high_opacity: "1",
		low_opacity: ".3",
		timing: "500"
	};
	var settings = $.extend(defaults, settings);
	settings.element = $(this);
			
	//set opacity to start
	$(settings.element).css("opacity",settings.start_opacity);
	//mouse over
	$(settings.element).hover(
	
		//mouse in
		function () {
		    $('.jfade_image').stop().animate({ opacity: settings.low_opacity }, settings.timing); 											  
			$(this).stop().animate({opacity: settings.high_opacity}, settings.timing); //100% opacity for hovered object
			
		},
		
		//mouse out
		function () {
			$(this).stop().animate({opacity: settings.start_opacity}, settings.timing); //return hovered object to start opacity
			$('.jfade_image').stop().animate({ opacity: settings.start_opacity }, settings.timing); // return other objects to start opacity
		}
	);
	return this;
	}
	
});