/**
 * @author Yen Chia Wei
 */
;(function($) {
	jQuery.fn.responsive_list_image = function(settings) {
		var image = $(this);
		// var image = container.children("img");
		
		image.wrap("<div class='nailthumb' style='width:100%;'></div>");
		var container = $(".nailthumb");
		container.prepend("<div class=\"image_standard\" style=\"width:100%;\"></div>");
		
		// var standard_width = $(".image_standard").width();
		// image.nailthumb({width:standard_width,height:standard_width,method:'crop',fitDirection:'center center',preload:false});
		
		$(window).resize(function(){

		    window.resizedFinished = setTimeout(function(){
		    	
		    	image.unwrap(".nailthumb-loading, .nailthumb-container");
		    	var standard_width = $(".image_standard").width();
				image.nailthumb({width:standard_width,height:standard_width,method:'crop',fitDirection:'center center',preload:false});

			}, 250);
			
		}).resize();
		
	}
})(jQuery); 