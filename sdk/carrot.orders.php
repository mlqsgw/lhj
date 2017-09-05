<?php
/**
 * Carrot MVC 订单系统SDK
 * @version 1.0.0
 * @author Johnny Zhang <48476265@qq.com>
 */

require('carrot.orders.config.php');
require('carrot.union.php');

class CarrotOrders extends CarrotUnion
{
	private $_order_id = null;  //订单号

	protected function _init() {
		$this->_union_id = CARROT_UNION_ID;
		$this->_union_key = CARROT_UNION_KEY;
	}

	//设置订单号
	public function order($id)
	{
		if (is_numeric($id)) {
			$this->_order_id = (int)$id;
		}
	}

	//创建订单
	public function create($order_data)
	{
		$default = array(
			'memo' => '',
			'type' => 0
		);
		$data = array_merge($default, $order_data);
		$signature = $this->make_signature_params();
		$url = $this->merge_url(CARROT_ORDERS_CREATE_URL, $signature);
		$res = $this->curl($url, $data);
		if (isset($res)) {
			$result = json_decode($res, true);
			if ($result['result']) {
				$this->_order_id = $result['order_id'];
			}
			return $result;
		} else {
			return null;
		}
	}

	//转让订单
	public function transfer($phone)
	{
		$result = array(
			'result' => false,
			'message' => ''
		);
		if (isset($this->_order_id)) {
			$data = array(
				'order' => $this->_order_id,
				'to_user' => $phone
			);
			$signature = $this->make_signature_params();
			$url = $this->merge_url(CARROT_ORDERS_TRANSFER_URL, $signature);
			$res = $this->curl($url, $data);
			if (isset($res)) {
				$result = json_decode($res, true);
			} else {
				$result['message'] = '请求失败';
			}
		} else {
			$result['message'] = '订单号没有设置';
		}
		return $result;
	}
}