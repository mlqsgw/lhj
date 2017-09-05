jQuery.Dialog = {
	tips:function(options){
		var settings = {
			title:'提示',
			message:'',
			width:'250px',
			height:'350px',
			sure:function(){
				
			},
			cancle:function(){
				
			}
		};
		options = $.extend(settings, options);
			var mask_layer = '';
				mask_layer = document.createElement("div");
				mask_layer.setAttribute("class","mask_layer");
				document.body.appendChild(mask_layer);
				var code = '<div class="shade"><div><div class="wrap" style="height:'+options["height"]+'; width:'+options["width"]+'"><div class="title">'+options["title"]+'</div><div class="content">'+options["content"]+'</div><div class="buttons"><a class="sure">'+"确定"+'</a><a class="cancle">'+"取消"+'</a></div></div>';
				$(".mask_layer").html(code);
			$(".shade").on('click',function(){
				$(".mask_layer").hide();
				
			});
			//回调方法
			$(".sure").on('click',function(){
				$(".mask_layer").hide();
				options.sure();
			});
			$(".cancle").on('click',function(){
				$(".mask_layer").hide();
				options.cancle();
			});
		
	},
	choose:function(param){
		var settings = {
			time:'',
			number:'',
			share:function(){
				
			},
			konw:function(){
				
			}
		};
		settings = $.extend(settings,param);
			var layer = '';
				layer = document.createElement("div");
				layer.setAttribute('class','layer');
				document.body.appendChild(layer);
				var code ='<div class="shade"></div><div class="contain"><p class="message">'+"恭喜您获得了一个抽奖号码，敬请关注明天下午"+settings["time"]+"点的开奖结果吧..."+'</p><p class="number">'+settings["number"]+'</p><div class="bt"><a class="share">'+"分享给好友"+'</a><a class="kown">'+"我知道了"+'</a></div><div class="tips"><span>'+"友情提示："+'</span>'+"每人每天都有一次0元抽奖机会，奖品丰富，记得每天来领取哦！！！"+'</div></div>';
				$('.layer').html(code);
				$(".share").on('click',function(){
					$(".layer").hide();
					settings.share();
					
				});
				$(".kown").on('click',function(){
					$(".layer").hide();
					settings.kown();
				});
				$(".shade").on('click',function(){
					$(".layer").hide();
				});
		
	}
};
