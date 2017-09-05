<?php
/**
 * Carrot MVC 合作平台抽象类
 * @version 1.0.0
 * @author Johnny Zhang <48476265@qq.com>
 */

require('carrot.union.config.php');

abstract Class CarrotUnion
{
	protected $_union_id = '';
	protected $_union_key = '';
	protected $_time;

	public function __construct()
	{
		$this->_time = time();
		$this->_init();
	}

	abstract protected function _init();

	//获取真实IP
	public function get_ip()
	{
		if (getenv('HTTP_CLIENT_IP')) {
			$ip = getenv('HTTP_CLIENT_IP');
		} elseif (getenv('HTTP_X_FORWARDED_FOR')) {
			$ip = getenv('HTTP_X_FORWARDED_FOR');
		} elseif (getenv('HTTP_X_FORWARDED')) {
			$ip = getenv('HTTP_X_FORWARDED');
		} elseif (getenv('HTTP_FORWARDED_FOR')) {
			$ip = getenv('HTTP_FORWARDED_FOR');

		} elseif (getenv('HTTP_FORWARDED')) {
			$ip = getenv('HTTP_FORWARDED');
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return substr($ip, 0, 50);
	}

	//生成签名参数
	public function make_signature_params()
	{
		$signature = md5($this->_union_id . $this->_union_key . $this->_time);
		return 'union_id=' . $this->_union_id . '&time=' . $this->_time . '&signature=' . $signature;
	}

	//在URL后追加参数
	public function merge_url($url, $params)
	{
		if (preg_match('/\?/', $url)) {
			return $url . '&' . $params;
		} else {
			return $url . '?' . $params;
		}
	}

	//发送请求
	public function curl($url, $params = null, $timeout = 10, $cookie = null, $user_agent = null, $referer = null, $username = null, $password = null)
	{
		if (preg_match('/^http[s]?:\/\//', $url)) {
			$method = isset($params) ? 'POST' : 'GET';
			$ch = curl_init();
			if ($method == 'POST') {
				curl_setopt($ch, CURLOPT_URL, $url);  //设置访问的URL
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
			} else {
				curl_setopt($ch, CURLOPT_URL, $url);  //设置访问的URL
			}
			curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);  //请求超时时间
			curl_setopt($ch, CURLOPT_HEADER, false);  //不输出文件头
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  //禁止 cURL 验证对等证书
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  //将curl_exec()获取的信息以字符串返回，而不是直接输出
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);  //根据服务器返回 HTTP 头中的 "Location: " 重定向
			curl_setopt($ch, CURLOPT_MAXREDIRS, 3);  //指定最多的 HTTP 重定向次数
			if (isset($cookie)) {
				curl_setopt($ch, CURLOPT_COOKIE, $cookie);  //多个 cookie 用分号分隔，分号后带一个空格(例如， "fruit=apple; colour=red")
			}
			//curl_setopt($ch, CURLOPT_COOKIESESSION, false);  //设为 TRUE 时将开启新的一次 cookie 会话。它将强制 libcurl 忽略之前会话时存的其他 cookie。 libcurl 在默认状况下无论是否为会话，都会储存、加载所有 cookie
			//curl_setopt($ch, CURLOPT_COOKIEJAR, '/cookie_file_path');  //连接结束后，保存 cookie 信息的文件
			//curl_setopt($ch, CURLOPT_COOKIEFILE, '/cookie_file_path');  //包含 cookie 数据的文件名，如果文件名是空的，不会加载 cookie，但 cookie 的处理仍旧启用
			if (isset($user_agent)) {
				curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);  //在HTTP请求中包含一个"User-Agent: "头的字符串
			} else {
				if (isset($_SERVER['HTTP_USER_AGENT'])) {
					curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);  //在HTTP请求中包含一个"User-Agent: "头的字符串
				}
			}
			if (!isset($referer)) {
				if (isset($_SERVER['HTTP_REFERER'])) {
					$referer = $_SERVER['HTTP_REFERER'];
				}
			}
			if (isset($referer)) {
				curl_setopt($ch, CURLOPT_REFERER, $_SERVER['HTTP_REFERER']);  //在HTTP请求头中"Referer: "的内容
			}
			if (isset($username) && isset($password)) {
				curl_setopt($ch, CURLOPT_USERPWD, $username . ':' . $password);  //传递一个连接中需要的用户名和密码，格式为：[username]:[password]
			}
			$data = curl_exec($ch);
			if (curl_errno($ch)) {
				$data = null;
			} elseif (curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200) {
				$data = null;
			}
			curl_close($ch);
			return $data;
		} else {
			return null;
		}
	}
}