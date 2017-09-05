(function($){
	$.fn.countDown = function(options){
	var sign,date_time,txt_id;
	var settings = {
		sign :"",
		date_time:"",
		txt_id:"",
		callback:function(){
			location.reload();
		}
	};
		settings = $.extend(settings,options);
		var endTime = settings["date_time"];
		var timer=setInterval(function(){
			var now = new Date();
			var leftSecond = endTime-parseInt(now.getTime()/1000);
			if(leftSecond>0){
				var day = Math.floor(leftSecond/(24*60*60));
				var hour = Math.floor(leftSecond%(24*60*60)/3600);
				var minute = Math.floor(leftSecond%(24*60*60)%3600/60);
				var second = Math.floor(leftSecond%(24*60*60)%3600%60);
				if(day<10) day = "0"+day;
				if(hour<10) hour = "0"+hour;
				if(minute<10) minute = "0"+minute;
				if(second<10) second = "0"+second;
				var shownTxt ="距离"+settings["sign"]+"还有"+day+"天 "+hour+":"+minute+":"+second;
			}else{
				var shownTxt = "已结束";
				if(leftSecond ==0){
					if(settings["sign"]=="开奖"){
						var url = settings["url"];
						var select_timer = setInterval(function(){
							$.ajax({
								url : url,
								data:{id:settings["id"]},
								success : function(data) {

									if (data['status']) {
										clearInterval(select_timer);
										select_timer = null;
										location.reload();
									}
								}
							});
						},5000);

					}else{
						settings.callback();
					}
					clearInterval(timer);
					timer=null;
				}
			}
				$("#"+settings["txt_id"]).html(shownTxt);
				},1000);
	}
})(jQuery);
