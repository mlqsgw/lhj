<?php
/**
 * Carrot MVC 支付通道配置
 * @version 1.0.0
 * @author Johnny Zhang <48476265@qq.com>
 */

define('CARROT_PAYMENT_URL', 'http://www.loyoubao.com/pay');  //支付平台URL
define('CARROT_PAYMENT_CREATE_URL', CARROT_PAYMENT_URL . '/pay.php');  //创建支付接口地址
define('CARROT_PAYMENT_QRCODE_URL', CARROT_PAYMENT_URL . '/qrcode.php');  //获取支付二维码接口地址