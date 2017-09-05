<?php
/**
 * Carrot MVC 消息通道配置
 * @version 1.0.0
 * @author Johnny Zhang <48476265@qq.com>
 */

define('CARROT_MESSAGE_URL', 'http://sso.loyoubao.com/message');  //消息平台URL
define('CARROT_MESSAGE_SMS_URL', CARROT_MESSAGE_URL . '/send_sms');  //发送短信接口
define('CARROT_MESSAGE_WECHAT_URL', CARROT_MESSAGE_URL . '/send_wechat_message');  //推送微信消息接口