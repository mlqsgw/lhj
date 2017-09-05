window.CarrotUI = {
	rules: {
		'phone': '^1[34578][0-9]{9}$'
	},
	//正则校验
	regMatch: function (str, pattern) {
		var re = new RegExp(pattern, 'gi');
		return re.test(str);
	},
	//HTML编码输出
	htmlEncode: function (str) {
		if (str) {
			str = str.replace(/&/g, '&amp;');
			str = str.replace(/</g, '&lt;');
			str = str.replace(/>/g, '&gt;');
			str = str.replace(/\"/g, '&quot;');
			return str;
		} else {
			return '';
		}
	},
	//合并URL
	merge_url: function (url, params) {
		if (this.regMatch('?', url)) {
			return url + '&' + params;
		} else {
			return url + '?' + params;
		}
	},
	//格式化时间
	fmtDate: function (timestamp) {
		var today = new Date();
		today.setHours(0);
		today.setSeconds(0);
		today.setMinutes(0);
		var today_timestamp = today.valueOf() / 1000;
		var d = new Date();
		d.setTime(timestamp * 1000);
		var dt = {
			month: (d.getMonth() + 1) < 10 ? '0' + (d.getMonth() + 1) : d.getMonth() + 1,
			day: d.getDate() < 10 ? '0' + d.getDate() : d.getDate(),
			hour: d.getHours() < 10 ? '0' + d.getHours() : d.getHours(),
			min: d.getMinutes() < 10 ? '0' + d.getMinutes() : d.getMinutes()
		};

		if (timestamp >= today_timestamp) {
			return '今天 ' +dt.hour + ':' + dt.min;
		} else if (timestamp + 86400 >= today_timestamp) {
			return '昨天 ' + dt.hour + ':' + dt.min;
		} else {
			return d.getFullYear() + '-' + dt.month + '-' + dt.day + ' ' + dt.hour + ':' + dt.min;
		}
	},
	//获取浏览器类型
	browser: function () {
		var ua = navigator.userAgent;
		if (this.regMatch(ua, 'MicroMessenger')) {
			return 'WECHAT';
		} else if (this.regMatch(ua, '(iphone|ipod|android|mobile|midp)')) {
			return 'MOBILE';
		} else {
			return 'PC';
		}
	},
	//提示文字
	tips: function (str) {
		var obj = document.createElement('div');
		obj.setAttribute('class', 'cui-tips');
		obj.innerHTML = '<span>' + str + '</span>';
		document.body.appendChild(obj);
		setTimeout(function () {
			$(obj).fadeOut(200, function () {
				obj.remove();
			});
		}, 200000)
	},
	//Loading
	loading: function () {
		if (!document.getElementById('overlay')) {
			var obj_overlay = document.createElement('div');
			obj_overlay.setAttribute('id', 'overlay');
			obj_overlay.setAttribute('class', 'cui-overlay');
			document.body.appendChild(obj_overlay);
		}
		var obj_loading = document.createElement('div');
		obj_loading.setAttribute('class', 'cui-loading');
		document.body.appendChild(obj_loading);
		$('.cui-dialog').remove();
	},
	dialog: function () {
		var _this = this;
		var defaults = {
			message: '',  //对话框内容
			buttons: [
				{
					caption: '确定',
					callback: function () {
						return true;
					}
				}
			]
		};
		var opts = $.extend(defaults, arguments[0]);
		if (!document.getElementById('overlay')) {
			var obj_overlay = document.createElement('div');
			obj_overlay.setAttribute('id', 'overlay');
			obj_overlay.setAttribute('class', 'cui-overlay');
			document.body.appendChild(obj_overlay);
		}
		var obj_dialog = document.createElement('div');
		obj_dialog.setAttribute('class', 'cui-dialog');
		var obj_message = document.createElement('div');
		obj_message.setAttribute('class', 'cui-dialog-message');
		obj_message.innerHTML = opts.message;
		obj_dialog.appendChild(obj_message);
		var obj_buttons = document.createElement('div');
		obj_buttons.setAttribute('class', 'cui-dialog-buttons');
		obj_dialog.appendChild(obj_buttons);
		document.body.appendChild(obj_dialog);
		for (var i in opts.buttons) {
			var a = document.createElement('a');
			a.setAttribute('href', 'javascript:;');
			$(a).data('id', i);
			a.innerHTML = opts.buttons[i]['caption'];
			a.addEventListener('click', function () {
				var id = $(this).data('id');
				if (opts.buttons[id].callback()) {
					_this.clean();
				}
			});
			obj_buttons.appendChild(a);
		}
	},
	//消息框
	alert: function (msg, fn) {
		var data = {message: msg};
		if (typeof fn == 'function') {
			data.buttons = [
				{
					caption: '确定',
					callback: function () {
						return fn();
					}
				}
			]
		}
		this.dialog({
			message: msg
		});
	},
	//确认框
	confirm: function (msg, fn) {
		this.dialog({
			message: msg,
			buttons: [
				{
					caption: '取消',
					callback: function () {
						return true;
					}
				},
				{
					caption: '确定',
					callback: function () {
						return fn();
					}
				}
			]
		});
	},
	//清除浮动层
	clean: function () {
		$('.cui-overlay').remove();
		$('.cui-dialog').remove();
		$('.cui-loading').remove();
	},
	//列表侧栏菜单
	listMenu: function (obj) {
		var $list = $(obj);
		var $menu = $list.children('.cui-list-menu');
		if ($menu.length > 0) {
			var pos = $list.offset();
			pos.left = pos.left + $list.width() - $menu.children('ul').eq(0).width();
			$menu.find('ul').css({
				top: pos.top,
				left: pos.left
			});
			if ($menu.is(':hidden')) {
				$menu.show();
			} else {
				$menu.hide();
			}
		}
	},
	//BASE64加密
	base64encode: function (str) {
		var base64EncodeChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';
		var out, i, len;
		var c1, c2, c3;
		len = str.length;
		i = 0;
		out = "";
		while (i < len) {
			c1 = str.charCodeAt(i++) & 0xff;
			if (i == len) {
				out += base64EncodeChars.charAt(c1 >> 2);
				out += base64EncodeChars.charAt((c1 & 0x3) << 4);
				out += "==";
				break;
			}
			c2 = str.charCodeAt(i++);
			if (i == len) {
				out += base64EncodeChars.charAt(c1 >> 2);
				out += base64EncodeChars.charAt(((c1 & 0x3) << 4) | ((c2 & 0xF0) >> 4));
				out += base64EncodeChars.charAt((c2 & 0xF) << 2);
				out += "=";
				break;
			}
			c3 = str.charCodeAt(i++);
			out += base64EncodeChars.charAt(c1 >> 2);
			out += base64EncodeChars.charAt(((c1 & 0x3) << 4) | ((c2 & 0xF0) >> 4));
			out += base64EncodeChars.charAt(((c2 & 0xF) << 2) | ((c3 & 0xC0) >> 6));
			out += base64EncodeChars.charAt(c3 & 0x3F);
		}
		return out;
	},
	//BASE64解密
	base64decode: function (str) {
		var base64DecodeChars = [-1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 62, -1, -1, -1, 63, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, -1, -1, -1, -1, -1, -1, -1, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, -1, -1, -1, -1, -1, -1, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, -1, -1, -1, -1, -1];
		var c1, c2, c3, c4;
		var i, len, out;
		len = str.length;
		i = 0;
		out = "";
		while (i < len) {
			/* c1 */
			do {
				c1 = base64DecodeChars[str.charCodeAt(i++) & 0xff];
			}
			while (i < len && c1 == -1);
			if (c1 == -1)
				break;
			/* c2 */
			do {
				c2 = base64DecodeChars[str.charCodeAt(i++) & 0xff];
			}
			while (i < len && c2 == -1);
			if (c2 == -1)
				break;
			out += String.fromCharCode((c1 << 2) | ((c2 & 0x30) >> 4));
			/* c3 */
			do {
				c3 = str.charCodeAt(i++) & 0xff;
				if (c3 == 61)
					return out;
				c3 = base64DecodeChars[c3];
			}
			while (i < len && c3 == -1);
			if (c3 == -1)
				break;
			out += String.fromCharCode(((c2 & 0XF) << 4) | ((c3 & 0x3C) >> 2));
			/* c4 */
			do {
				c4 = str.charCodeAt(i++) & 0xff;
				if (c4 == 61)
					return out;
				c4 = base64DecodeChars[c4];
			}
			while (i < len && c4 == -1);
			if (c4 == -1)
				break;
			out += String.fromCharCode(((c3 & 0x03) << 6) | c4);
		}
		return out;
	}
};

//微信设置
window.CarrotWechat = {
	is_init: false,
	is_load_api: false,
	is_set_config: false,
	user_id: null,
	init: function (fn) {
		var _this = this;
		if (this.is_init) {
			if (typeof fn == 'function') {
				fn();
			}
		} else {
			if (CarrotUI.browser() == 'WECHAT') {
				if (this.is_load_api) {
					_this.setConfig(fn);
				} else {
					$.getScript('http://res.wx.qq.com/open/js/jweixin-1.2.0.js').done(function () {
						_this.is_load_api = true;
						_this.setConfig(fn);
					});
				}
			}
		}
	},
	setConfig: function (fn) {
		var _this = this;
		$.ajax({
			type: 'GET',
			url: 'http://sso.loyoubao.com/wechat/jsapi_config',
			dataType: 'jsonp',
			data: {url: window.location.href},
			jsonp: 'callback',
			success: function (data) {
				if (data['result']) {
					wx.config({
						debug: false,
						appId: data['config']['appId'],
						timestamp: data['config']['timestamp'],
						nonceStr: data['config']['nonceStr'],
						signature: data['config']['signature'],
						jsApiList: ['onMenuShareTimeline', 'onMenuShareAppMessage', 'onMenuShareQQ', 'onMenuShareWeibo', 'onMenuShareQZone']
					});
					_this.user_id = data['config']['uid'];
					_this.is_init = true;
					if (typeof fn == 'function') {
						fn();
					}
				}
			}
		});
	},
	setShare: function (callback) {
		var _this = this;
		this.init(function () {
			var title, description, url, img;
			var $meta_title = $('meta[name=share-title]');
			if ($meta_title.length > 0) {
				title = $meta_title.attr('content');
			} else {
				title = '龙有宝';
			}
			var $meta_description = $('meta[name=description]');
			if ($meta_description.length > 0) {
				description = $meta_description.attr('content');
			} else {
				description = '致力于快乐的购物体验';
			}
			var $meta_image = $('meta[name=share-image]');
			if ($meta_image.length > 0) {
				img = $meta_image.attr('content');
			} else {
				img = 'http://res.loyoubao.com/static/images/logo.png';
			}
			url = _this.user_id ? CarrotUI.merge_url(window.location.href, 'linker=' + _this.user_id) : window.location.href;
			var rd_url = 'http://sso.loyoubao.com/redirect/' + CarrotUI.base64decode(url);
			wx.ready(function(){
				//分享朋友圈
				wx.onMenuShareTimeline({
					title: title, // 分享标题
					link: rd_url, // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
					imgUrl: img, // 分享图标
					success: function () {
						if (typeof callback == 'function') {
							callback();
						}
					},
					cancel: function () {
						// 用户取消分享后执行的回调函数
					}
				});
				//分享给朋友
				wx.onMenuShareAppMessage({
					title: title, // 分享标题
					desc: description, // 分享描述
					link: rd_url, // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
					imgUrl: img, // 分享图标
					type: '', // 分享类型,music、video或link，不填默认为link
					dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
					success: function () {
						if (typeof callback == 'function') {
							callback();
						}
					},
					cancel: function () {
						// 用户取消分享后执行的回调函数
					}
				});
				//分享到QQ
				wx.onMenuShareQQ({
					title: title, // 分享标题
					desc: description, // 分享描述
					link: rd_url, // 分享链接
					imgUrl: img, // 分享图标
					success: function () {
						if (typeof callback == 'function') {
							callback();
						}
					},
					cancel: function () {
						// 用户取消分享后执行的回调函数
					}
				});
				//分享到腾讯微博
				wx.onMenuShareWeibo({
					title: title, // 分享标题
					desc: description, // 分享描述
					link: rd_url, // 分享链接
					imgUrl: img, // 分享图标
					success: function () {
						if (typeof callback == 'function') {
							callback();
						}
					},
					cancel: function () {
						// 用户取消分享后执行的回调函数
					}
				});
				//分享到QQ控件
				wx.onMenuShareQZone({
					title: title, // 分享标题
					desc: description, // 分享描述
					link: rd_url, // 分享链接
					imgUrl: img, // 分享图标
					success: function () {
						if (typeof callback == 'function') {
							callback();
						}
					},
					cancel: function () {
						// 用户取消分享后执行的回调函数
					}
				});
				_this.is_set_config = true;
			});
		});
	}
};

(function ($) {
	$.fn.extend({
		//表单简单验证
		validate: function (options) {
			var defaults = {
				trim: true,  //是否去掉输入内容左右的空格
				required: [],  //必填项
				onSend: null,  //如果设置成function，则在表单提交前执行
				callback: null,  //如设置成function，则以ajax方式提交
				dataType: 'json'  //ajax返回类型
			};
			var opts = $.extend(defaults, options);
			return this.each(function () {
				var $this = $(this);
				$this.on('submit', function (event) {
					event.preventDefault();
					$this.find(':input.submit').prop('disabled', true);
					var params = {};
					$this.find(':input[name]').each(function () {
						$(this).removeClass('cui-error');
						var name = $(this).attr('name');
						var val = null;
						if ($(this).attr('type') == 'radio' || $(this).attr('type') == 'checkbox') {
							if ($(this).prop('checked')) val = $(this).val();
						} else {
							val = opts.trim ? $.trim($(this).val()) : $(this).val();
						}
						if (val !== null && val !== '') {
							if (params.hasOwnProperty(name)) {
								params[name] += ',' + val;
							} else {
								params[name] = val;
							}
						}
					});
					var result = true;
					for (var i in opts.required) {
						if (!params.hasOwnProperty(opts.required[i])) {
							$this.find(':input[name=' + opts.required[i] + ']').addClass('cui-error');
							result = false;
						}
					}
					if (result) {
						if (typeof opts.onSend == 'function') {
							opts.onSend();
						}
						if (typeof opts.callback == 'function') {
							var url = $this.attr('action'),
								method = $this.attr('method');
							if (!method) method = 'get';
							$.ajax({
								type: method,
								url: url,
								data: params,
								dataType: opts.dataType,
								success: function (data) {
									opts.callback(data);
									setTimeout(function () {
										$this.find(':input.submit').prop('disabled', false);
									}, 2000);
								},
								error: function () {
									$this.find(':input.submit').prop('disabled', false);
									CarrotUI.tips('Submit failed');
								}
							});
						} else {
							$this.get(0).submit();
						}
					} else {
						$this.find(':input.submit').prop('disabled', false);
					}
					return false;
				});
			});
		},
		//滑动轮播图
		slider: function () {
			return this.each(function () {
				var $this = $(this);
				var delay = parseInt($this.attr('data-delay'));
				if (isNaN(delay)) {
					delay = 5000;
				}
				var speed = parseInt($this.attr('data-speed'));
				if (isNaN(speed)) {
					speed = 200;
				}
				var height = $this.height();
				var $content;
				var $page;
				var $ul = $this.children('ul');
				var $li = $ul.children('li');
				var width = 0;
				var count = $li.length;
				var tmr = null;
				var pointer = 0;
				function _set_page() {
					if (count > 1) {
						var content = document.createElement('div');
						content.setAttribute('class', 'cui-slider-content');
						var touch_position = null;
						var play_type;
						content.addEventListener('touchstart', function (event) {
							clearTimeout(tmr);
							$(this).stop();
							if (event.targetTouches.length == 1) {
								var touch = event.targetTouches[0];
								touch_position = {
									x: touch.screenX,
									y: touch.screenY,
									X: this.scrollLeft
								};
							}
						});
						content.addEventListener('touchmove', function (event) {
							if (event.targetTouches.length == 1) {
								var touch = event.targetTouches[0];
								if (touch_position) {
									if (Math.abs(touch_position.x - touch.screenX) > Math.abs(touch_position.y - touch.screenY)) {
										event.preventDefault();
										var move = touch_position.x - touch.screenX;
										this.scrollLeft = touch_position.X + move;
										if (move > 0) {
											play_type = 'next';
										} else if (move < 0) {
											play_type = 'prev';
										}
									} else {
										touch_position = null;
									}
								}
							}
						});
						content.addEventListener('touchend', function (event) {
							touch_position = null;
							_go_to(play_type);
						});
						$content = $(content);
						$this.append($content);
						$ul.append($ul.html());
						$content.append($ul);
						$page = $('<div class="cui-slider-page"></div>');
						for (var i = 0; i < count; i++) {
							var oSpan = document.createElement('span');
							oSpan.setAttribute('data-num', i);
							oSpan.onclick = function () {
								_go_to(this);
							};
							if (i == 0) {
								oSpan.setAttribute('class', 'active');
							}
							$page.append(oSpan);
						}
						$this.append($page);
					}
					$(window).resize(function () {
						_set_size();
					});
					_set_size();
				}
				function _set_size() {
					width = $this.width();
					if (height > 0) {
						$this.height(height);
					} else {
						$this.height(width);
					}
					if (count > 1) {
						$ul.width(width * count * 2).show(0).children().width(width).css({float:'left'});
						clearTimeout(tmr);
						pointer = 0;
						$content.scrollLeft(0);
						_play();
					}
				}
				function _play() {
					tmr = setTimeout(function () {
						_go_to();
					}, delay);
				}
				function _go_to(obj) {
					clearTimeout(tmr);
					if (obj == 'object') {
						pointer = parseInt($(obj).attr('data-num'));
					} else if (obj == 'next') {
						pointer++;
					} else if (obj == 'prev') {
						pointer--;
						if (pointer < 0) {
							pointer = 0;
						}
					} else {
						pointer++;
					}
					$page.children().removeClass('active').eq((pointer >= count ? 0 : pointer)).addClass('active');
					$content.stop();
					$content.animate({scrollLeft: (pointer * width)}, speed, function () {
						if (pointer >= count) {
							pointer = 0;
							$content.scrollLeft(0);
						}
						_play();
					});
				}
				if (count > 0) {
					_set_page();
				}
			});
		},
		//cui-group tabs切换
		groupTabs: function (fn) {
			return this.each(function () {
				var _tab = this;
				var $parts = $(_tab).siblings('.cui-group-part');
				function _show_part(i) {
					$(_tab).find('li').removeClass('active').eq(i).addClass('active');
					$parts.hide(0);
					if ($parts.length > i) {
						$parts.eq(i).show(0);
					}
					if (typeof fn == 'function') {
						fn(i);
					}
				}
				_show_part(0);
				$(this).find('li').on('click', function () {
					var _this = this;
					var $ul = $(this).parent();
					$ul.children().each(function (i) {
						if (this == _this) {
							_show_part(i);
						}
					});
				});
			});
		}
	});
})(jQuery);

$.ajaxSetup({cache: false});

$(function () {
	$('.cui-list-type ul li').on('click', function () {
		var $this = $(this);
		if (!$this.hasClass('active')) {
			$this.addClass('active').siblings('li').removeClass('active');
		}
	});
	$('.cui-list-menu-button').on('click', function () {
		var $list = $(this).parents('.cui-list');
		if ($list.length > 0) {
			CarrotUI.listMenu($list.get(0));
		}
	});
	$('.cui-slider').slider();
	setTimeout(function () {
		if (!CarrotWechat.is_set_config) {
			CarrotWechat.setShare();
		}
	}, 5000)
});