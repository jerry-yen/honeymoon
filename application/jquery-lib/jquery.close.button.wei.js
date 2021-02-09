/**
 * @author Yen Chia Wei
 */
;(function($) {
	jQuery.fn.closebutton = function(settings) {
		var _defaultSettings = {
			show : "X",
			close : function() {
			}
		};
		var _settings = $.extend(_defaultSettings, settings);

		// 將所有相關關閉連結都拿掉
		$(".closebutton",this).remove();
		$(".command_bar",this).remove();

		$(this).css({ "position" : "relative" });

		$(this).append("<div class=\"closebutton\"><a href=\"javascript:void(0);\" style=\"color:white;\">刪 除</a></div>");
		$(".closebutton").css({"position" : "absolute"});
		$(".closebutton").css({"text-align" : "center"});
		$(".closebutton").css({"background-color" : "gray"});
		$(".closebutton").css({"width":"100%"});
		$(".closebutton").css({"top" : "0px"});
		$(".closebutton").hide();
		

		$(this).children(".closebutton").click(function() {
			_settings.close($(this).parent());
		});
		
		
		$(this).append("<div class=\"command_bar\"><a href=\"javascript:void(0);\" style=\"color:white;\">放 大 檢 視</a></div>");
		$(".command_bar").css({"position" : "absolute"});
		$(".command_bar").css({"text-align" : "center"});
		$(".command_bar").css({"background-color" : "gray"});
		$(".command_bar").css({"width":"100%"});
		$(".command_bar").css({"bottom" : "0px"});
		$(".command_bar").hide();
		
		$(this).each(function(){
			var class_name = $(this).attr("data-group");
			var image_src = $(this).attr("data-src");
			$(this).children(".command_bar").children("a").addClass(class_name);
			$(this).children(".command_bar").children("a").attr("href", image_src);
		});
		
		
		$(this).mouseover(function() {
			$(this).children(".closebutton").show();
			$(this).children(".command_bar").show();
			
			
		});
		$(this).mouseout(function() {
			$(this).children(".closebutton").hide();
			$(this).children(".command_bar").hide();
		});
		
		$(this).css({"border" : "1px gray solid"});
		$(this).css({"margin" : "5px"});

		this.remove = function() {
        	console.log("remove");
    	};

	}
})(jQuery); 