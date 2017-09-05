(function($){
	$.fn.scollBar=function(options){
		var content_box_id,content_id,content_scroll_id,speed;
		var settings={
			content_box_id:'',
			content_id:'',
			content_scroll_id:'',
			speed:''
		};
		settings = $.extend(settings,options);
		var content_box = document.getElementById(settings['content_box_id']);
		var content = document.getElementById(settings['content_id']);
		var content_scroll = document.getElementById(settings['content_scroll_id']);
		function scoll_animation(){
			content_scroll.innerHTML = content.innerHTML;
			var scoll = content_scroll.offsetTop;
			var con = content.offsetHeight;
			var box = content_box.scrollTop-20;
			var value =scoll-box;
			if(value <=25){
				$("#"+settings['content_scroll_id']).append($("#"+settings['content_id']).clone());
				box -= con;
			}else{
					box += 40;
			}
			content_box.scrollTop = box;
			content.offsetHeight = con;
			content_scroll.offsetTop = scoll;
		}
		var timer = setInterval(scoll_animation,settings['speed']);
		content_box.onmouseover = function(){
			clearInterval(timer);
		}
		content_box.onmouseout = function(){
			timer = setInterval(scoll_animation,settings['speed']);
		}
	}
})(jQuery);
