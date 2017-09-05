<?php
/**
 * Carrot MVC 支付通道SDK
 * @version 1.0.0
 * @author Johnny Zhang <48476265@qq.com>
 */

require_once('carrot.payment.config.php');
require_once('carrot.union.php');

class CarrotPayment extends CarrotUnion
{
	protected function _init() {
		$this->_union_id = CARROT_UNION_ID;
		$this->_union_key = CARROT_UNION_KEY;
	}

	//生成订单参数并开始支付
	public function pay($order)
	{
		$signature = $this->make_pay_signature_params($order);
		$url = $this->merge_url(CARROT_PAYMENT_CREATE_URL, $signature);
		$html = '<!DOCTYPE html><html><head><meta http-equiv="Content-type" content="text/html; charset=utf-8" /><meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0,user-scalable=no" /><meta http-equiv="X-UA-Compatible" content="IE=edge" /><title></title><script type="text/javascript">window.onload=function(){document.form1.submit();};</script></head><body>';
		$html .= '<form name="form1" action="' . $url . '" method="post">';
		$html .= '<input type="hidden" name="user_id" value="' . $order['user_id'] . '" />';
		$html .= '<input type="hidden" name="description" value="' . $order['description'] . '" />';
		$html .= '<input type="hidden" name="goods_id" value="' . $order['goods_id'] . '" />';
		$html .= '<input type="hidden" name="goods_name" value="' . $order['goods_name'] . '" />';
		$html .= '<input type="hidden" name="goods_type" value="' . $order['goods_type'] . '" />';
		$html .= '<input type="hidden" name="amount" value="' . $order['amount'] . '" />';
		$html .= '<input type="hidden" name="pay_purposes" value="' . $order['pay_purposes'] . '" />';
		$html .= '<input type="hidden" name="order_code" value="' . $order['order_code'] . '" />';
		$html .= '<input type="hidden" name="order_id" value="' . $order['order_id'] . '" />';
		$html .= '<input type="hidden" name="notify_url" value="' . $order['notify_url'] . '" />';
		$html .= '<input type="hidden" name="return_url" value="' . $order['return_url'] . '" />';
		$html .= '</form></body></html>';
		echo $html;
		exit;
	}

	//从支付平台获取支付二维码
	public function qrcode($order)
	{
		$signature = $this->make_pay_signature_params($order);
		$url = $this->merge_url(CARROT_PAYMENT_QRCODE_URL, $signature);
		$result = $this->curl($url, $order);
		if ($result) {
			return json_decode($result, true);
		} else {
			return null;
		}
	}

	//生成支付签名
	public function make_pay_signature_params($order)
	{
		$signature = md5($order['description'] . $order['amount'] . $this->_union_id . $this->_union_key . $this->_time);
		return 'union_id=' . $this->_union_id . '&time=' . $this->_time . '&signature=' . $signature;
	}

	//生成随机订单号
	public function make_order_id($prefix = '')
	{
		list($u_sec, $sec) = explode(" ", microtime());  //获取时间戳和毫秒
		$order1 = date('YmdHis', $sec);
		$u_num = (int)($u_sec * 10000);
		if ($u_num < 10) {
			$order2 = '000' . $u_num;
		} elseif ($u_num < 100) {
			$order2 = '00' . $u_num;
		} elseif ($u_num < 1000) {
			$order2 = '0' . $u_num;
		} else {
			$order2 = (string)$u_num;
		}
		$rnd = rand(0, 9999);
		if ($rnd < 10) {
			$order = $order1 . $order2 . '000' . $rnd;
		} elseif ($rnd < 100) {
			$order = $order1 . $order2 . '00' . $rnd;
		} elseif ($rnd < 1000) {
			$order = $order1 . $order2 . '0' . $rnd;
		} else {
			$order = $order1 . $order2 . $rnd;
		}
		return $prefix . $order;
	}

	//生成随机序号(32Byte)
	public function make_random_code()
	{
		return md5(uniqid('', true));
	}

	//获取浏览器类型
	public function browser()
	{
		$ua = $_SERVER['HTTP_USER_AGENT'];
		if (preg_match('/MicroMessenger/', $ua)) {
			return 'WECHAT';
		} else {
			if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
				return 'MOBILE';
			}
			if (isset($_SERVER['HTTP_VIA'])) {
				if (stristr($_SERVER['HTTP_VIA'], 'wap')) {
					return 'MOBILE';
				}
			}
			if (preg_match("/(iphone|ipod|android|mobile|midp)/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
				return 'MOBILE';
			}
			if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
				return 'MOBILE';
			}
			return 'PC';
		}
	}
}