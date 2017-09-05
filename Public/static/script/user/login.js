$(function () {
	var $form = $(document.form);
	$form.validate({
		required: ['phone', 'code'],
		callback: function (data) {
			if (data['result']) {
				if (data['return_url']) {
					window.open(data['return_url'], '_self');
				} else {
					window.open('/users/', '_self');
				}
			} else {
				CarrotUI.tips(data['message']);
			}
		}
	});
	var timer = null;
	var time_count = 0;
	var csrf = $form.find(':input[name=csrfmiddlewaretoken]').val();
	var $get_auth_code = $('#get_auth_code');
	$form.find(':input[name=phone],:input[name=code]').on('blur', function () {
		$(this).removeClass('cui-error');
	});
	$get_auth_code.on('click', function () {
		var $this = $(this);
		var $phone = $form.find(':input[name=phone]');
		var phone = $phone.val();
		if (CarrotUI.regMatch(phone, CarrotUI.rules['phone'])) {
			$this.prop('disabled', true);
			$.ajax({
				type: 'POST',
				url: get_auth_code_url,
				data: {'csrfmiddlewaretoken': csrf, 'phone': phone},
				dataType: 'json',
				success: function (data) {
					if (data['result']) {
						time_count = 60;
						$this.val('重新获取(' + time_count + ')');
						timer = setInterval(function () {
							if (time_count <= 1) {
								clearInterval(timer);
								$this.val('重新获取').prop('disabled', false);
							} else {
								time_count--;
								$this.val('重新获取(' + time_count + ')');
							}
						}, 1000);
					} else {
						CarrotUI.tips(data['message']);
						$this.prop('disabled', false);
					}
				},
				error: function () {
					CarrotUI.tips('请求失败');
					$this.prop('disabled', false);
				}
			});
		} else {
			$phone.addClass('cui-error');
		}
	});
});