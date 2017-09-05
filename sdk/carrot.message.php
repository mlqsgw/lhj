<?php
/**
 * Carrot MVC 消息通道SDK
 * @version 1.0.0
 * @author Johnny Zhang <48476265@qq.com>
 */

require('carrot.message.config.php');
require('carrot.union.php');

class CarrotMessage extends CarrotUnion
{
	protected function _init() {
		$this->_union_id = CARROT_UNION_ID;
		$this->_union_key = CARROT_UNION_KEY;
	}

	//发送短信给指定号码
	public function send_sms2phone($phone, $template_id, $data, $ip = '0')
	{
		$signature = $this->make_signature_params();
		$url = $this->merge_url(CARROT_MESSAGE_SMS_URL, $signature);
		$data = array(
			'phone' => $phone,
			'template_id' => $template_id,
			'data' => json_encode($data),
			'ip' => $ip
		);
		$result = $this->curl($url, $data);
		return $result;
	}

	//发送短信给用户
	public function send_sms($user_id, $template_id, $data, $ip = '0')
	{
		$signature = $this->make_signature_params();
		$url = $this->merge_url(CARROT_MESSAGE_SMS_URL, $signature);
		$data = array(
			'user_id' => $user_id,
			'template_id' => $template_id,
			'data' => json_encode($data),
			'ip' => $ip
		);
		$result = $this->curl($url, $data);
		return $result;
	}

	//推送微信消息给指定openid
	public function send_wechat_message2openid($open_id, $template_id, $data, $ip = '0')
	{
		$signature = $this->make_signature_params();
		$url = $this->merge_url(CARROT_MESSAGE_WECHAT_URL, $signature);
		$data = array(
			'open_id' => $open_id,
			'template_id' => $template_id,
			'data' => json_encode($data),
			'ip' => $ip
		);
		$result = $this->curl($url, $data);
		return $result;
	}

	//推送微信消息给用户
	public function send_wechat_message($user_id, $template_id, $data, $ip = '0')
	{
		$signature = $this->make_signature_params();
		$url = $this->merge_url(CARROT_MESSAGE_WECHAT_URL, $signature);
		$data = array(
			'user_id' => $user_id,
			'template_id' => $template_id,
			'data' => json_encode($data),
			'ip' => $ip
		);
		$result = $this->curl($url, $data);
		return $result;
	}
}