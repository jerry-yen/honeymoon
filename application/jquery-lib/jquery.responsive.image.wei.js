/**
 * @author Yen Chia Wei
 */
;(function($) {
	jQuery.fn.responsive_image = function(settings) {
		var container = $(this);
		var source_images_width = new Array();
		$("img",container).each(function(index){
			var img = $(this);
			var final_width = parseInt(img.attr("width"));
			if(isNaN(final_width)){
				img.removeAttr("width");
				img.removeAttr("height");
				img.css("width", "auto");
				img.css("height","auto");
				source_images_width[index] = img.width();
			}
			else{
				source_images_width[index] = final_width;
			}
			
		});
		
			
		$(window).resize(function(){
				var container_width = container.width();
				$("img",container).each(function(index){
					
					var final_width = "100%";
					
					if(source_images_width[index] > container_width){
						final_width = "100%";
					}
					else{
						final_width = source_images_width[index];
					}
					
					var img = $(this);	
					img.removeAttr("width");
					img.removeAttr("height");
					img.css("width", final_width);
					img.css("height","auto");
				});
		}).resize();
	}
})(jQuery); 