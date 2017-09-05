<?php
return array(
	//'配置项'=>'配置值'
	'default_module'     => 'Home', //默认模块
	'url_model'          => '2', //URL模式
	'session_auto_start' => true, //是否开启session
	'LOAD_EXT_CONFIG' => 'db',// 加载单独的数据库配置文件
	'URL_CASE_INSENSITIVE' => true,// url忽略大小写

    'TMPL_L_DELIM'          => '<{',   // 模板引擎普通标签开始标记
    'TMPL_R_DELIM'          => '}>',   // 模板引擎普通标签结束标记


	//设置中奖率
	'draw_one' => '0.01', //一奖中奖率
	'draw_two' => '0.03', //二等奖中奖率
	'draw_three' => '0.07', //三等奖中奖率
	'draw_four' => '0.09', //四等奖中奖率

	//设置中奖倍数
	'draw_one_multiple' => '10', //一等奖中奖倍数
	'draw_two_multiple' => '5', //二等奖中奖倍数
	'draw_three_multiple' => '2', //三等奖中奖倍数
    'draw_four_multiple' => '1', //四等奖中奖倍数

);