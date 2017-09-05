<?php
namespace Home\Controller;
use Think\Controller;

require('sdk/carrot.sso.php');
require('sdk/union.php');

class IndexController extends Controller {
    //获取登录用户信息
    public function if_login(){
        //获取用户登陆信息

        \CarrotSSO::start_session();
        if (!isset($_SESSION['user'])) {  //如果用户SESSION不存在，尝试SSO登录
            $user_info = \CarrotSSO::auth();
            if (isset($user_info)) {
                $_SESSION['user'] = $user_info;  //分配SESSION
            } else {
                //TODO: 无法通过授权获取用户资料，输出错误页面或重定向到其他不需要登录的页面
                echo '单点登录失败';
                exit;
            }
        }
    }

    //获取用户信息
    public function get_user_data(){
        \CarrotSSO::start_session();
        if (!isset($_SESSION['user'])) {  //如果用户SESSION不存在，尝试SSO登录
            $user_info = \CarrotSSO::auth();
            if (isset($user_info)) {
                $_SESSION['user'] = $user_info;  //分配SESSION
                $this->redirect('index');
            } else {
                //TODO: 无法通过授权获取用户资料，输出错误页面或重定向到其他不需要登录的页面
                echo '单点登录失败';
                exit;
            }
        }
    }

    //空操作方法
    public function _empty(){
        $this->error("这个是个空的操作方法",'',6);
    }

    //获取抽奖阙值
    public function hold_value(){
        $m_quezhi = D('quezhi');
        $type = 1;
        $hold_value_data = $m_quezhi->get_hold_value_data($type);
        $hold_value = $hold_value_data['hold_value'];
        return $hold_value;
    }

    //老虎机首页面
    public function index(){
        $union = new \MyUnion();
        $u_id = $_SESSION['user']['uid'];

        if(isset($u_id)) {
            $signature = $union->make_signature_params(); //生成签名参数
            $union_id = "LYS"; //合作平台id
            $time = time(); //当前时间戳
            $url = CARROT_SSO_URL."/users/coins_info?user_id=".$u_id."&union_id=".$union_id."&time=".$time."&signature=".$signature;
            $user_coins_data = json_decode($union->curl($url),true); //用户积分数据
            $user_coins = $user_coins_data["coins"]; //用户积分

        } else {
            $user_coins = 0;
        }

        $m_log = D('log');
        $where["change_status"] = 1;
        $draw_data = $m_log->where($where)->order('id desc')->limit(100)->select();

        $m_users = D('users');
        foreach ($draw_data as $key=>$value) {
            $draw_data[$key]['data'] = $m_users->get_users($value['u_id']);
        }

        $this->assign("draw_data", $draw_data);
        $this->assign("user_money", $user_coins);
        $this->display();
    }

    //触发游戏
    public function do_game() {//中奖状态  draw_status   0 金币不足  1 不中奖  2 一等奖  3 特等奖
        $union = new \MyUnion();
        $hold_value = $this->hold_value();
        if(!$hold_value){
            $hold_value = 1;
        }

        if (IS_AJAX) {

            $this->if_login();
            $u_id = $_SESSION['user']['uid'];

            //设置中奖率
            $draw_one = C('draw_one');   //一等奖中奖率
            $draw_two = C('draw_two');   //二等奖中奖率
            $draw_three = C('draw_three');   //三等奖中奖率
            $draw_four = C('draw_four');   //四等奖中奖率

            //设置中奖倍数
            $draw_one_multiple = C('draw_one_multiple'); //一等奖中奖倍数
            $draw_two_multiple = C('draw_two_multiple'); //二等奖中奖倍数
            $draw_three_multiple = C('draw_three_multiple'); //三等奖中奖倍数
            $draw_four_multiple = C('draw_four_multiple'); //四等奖中奖倍数

            $draw_one_probability = $draw_one * 10000 * $hold_value;
            $draw_two_probability = ($draw_one+$draw_two) * 10000 * $hold_value;
            $draw_three_probability = ($draw_one+$draw_two+$draw_three) * 10000 * $hold_value;
            $draw_four_probability = ($draw_one+$draw_two+$draw_three+$draw_four) * 10000 * $hold_value;

            $money = $_GET["money"]; //获取下注的金币额
            $balance = $_GET["user_code"]; //用户余额
            $balance_two = $balance + $money; //用户扣除前的余额

            if ($money < 10) {
                $return = array(
                    "draw_status" => 0,
                    "message" => "下注金额不能小于10积分"
                );
                $return['draw_num'] = array();
            } else if ($money > 10000) {
                $return = array(
                    "draw_status" => 0,
                    "message" => "下注金额不能大于10000积分"
                );
                $return['draw_num'] = array();
            } else if ($balance_two < $money) {
                $return = array(
                    "draw_status" => 0,
                    "message" => "当前余额小于下注金额，请先充值！"
                );
            }else {
                $signature = $union->make_signature_params(); //生成签名参数
                $union_id = "LYS"; //合作平台id
                $time = time(); //当前时间戳
                $url = CARROT_SSO_URL."/users/coins_info?user_id=".$u_id."&union_id=".$union_id."&time=".$time."&signature=".$signature;
                $user_coins_data = json_decode($union->curl($url),true); //用户积分数据
                $user_coins = $user_coins_data["coins"]; //用户积分
                $user_coins = $user_coins + $money;//下注前用户余额

                //判断用户金币额是否足够
                if ($user_coins >= $money){
                    $num = rand(0,10000);

                    if ($num <= $draw_one_probability){

                        $draw_add_num = $money * $draw_one_multiple;
                        $content = "用户获得一等奖";

                        //调用金币增加接口
                        $url = CARROT_SSO_URL."/users/coins_change?union_id=".$union_id."&time=".$time."&signature=".$signature;
                        $params = "user_id=".$u_id."&type=1&amount=".$draw_add_num."&content=".$content;
                        $user_coins_data_add = json_decode($union->curl($url,$params),true); //增加接口返回数据

                        if ($user_coins_data_add["result"]){
                            $return = array(
                                "draw_status" => 1,
                                "message" => ""
                            );

                            $change_status = 1; //金额变化状态
                            //添加抽奖日志
                            $this->add_log($u_id, $draw_add_num, $return['draw_status'], $change_status);

                        }
                    } else if ($num > $draw_one_probability && $num <=$draw_two_probability) {

                        $draw_add_num = $money * $draw_two_multiple;
                        $content = "用户获得二等奖";

                        //调用金币增加接口
                        $url = CARROT_SSO_URL."/users/coins_change?union_id=".$union_id."&time=".$time."&signature=".$signature;
                        $params = "user_id=".$u_id."&type=1&amount=".$draw_add_num."&content=".$content;
                        $user_coins_data_add = json_decode($union->curl($url,$params),true); //增加接口返回数据

                        if ($user_coins_data_add["result"]){
                            $return = array(
                                "draw_status" => 2,
                                "message" => ""
                            );

                            $change_status = 1; //金额变化状态
                            //添加抽奖日志
                            $this->add_log($u_id, $draw_add_num, $return['draw_status'], $change_status);
                        }

                    } else if ($num > $draw_two_probability && $num <=$draw_three_probability) {

                        $draw_add_num = $money * $draw_three_multiple;
                        $content = "用户获得三等奖";

                        //调用金币增加接口
                        $url = CARROT_SSO_URL."/users/coins_change?union_id=".$union_id."&time=".$time."&signature=".$signature;
                        $params = "user_id=".$u_id."&type=1&amount=".$draw_add_num."&content=".$content;
                        $user_coins_data_add = json_decode($union->curl($url,$params),true); //增加接口返回数据

                        if ($user_coins_data_add["result"]){
                            $return = array(
                                "draw_status" => 3,
                                "message" => ""
                            );

                            $change_status = 1; //金额变化状态
                            //添加抽奖日志
                            $this->add_log($u_id, $draw_add_num, $return['draw_status'], $change_status);
                        }
                    } else if ($num > $draw_three_probability && $num <= $draw_four_probability ) {

                        $draw_add_num = $money * $draw_four_multiple;
                        $content = "用户获得四等奖";
                        //调用金币增加接口
                        $url = CARROT_SSO_URL."/users/coins_change?union_id=".$union_id."&time=".$time."&signature=".$signature;
                        $params = "user_id=".$u_id."&type=1&amount=".$draw_add_num."&content=".$content;
                        $user_coins_data_add = json_decode($union->curl($url,$params),true); //增加接口返回数据

                        if ($user_coins_data_add["result"]){
                            $return = array(
                                "draw_status" => 4,
                                "message" => ""
                            );

                            $change_status = 1; //金额变化状态
                            //添加抽奖日志
                            $this->add_log($u_id, $draw_add_num, $return['draw_status'], $change_status);
                        }
                    } else {
                        $return = array(
                            "draw_status" => 5,
                            "message" => ""
                        );
                    }
                } else {
                    $return = array(
                        "draw_status" => 0,
                        "message" => "金币不足，请先充值"
                    );
                }

            }
        } else {
            echo "错误操作";
        }

        if ($return["draw_status"]) {
            $draw_result = $this->do_draw($return["draw_status"]);
            $return['draw_num'] = implode('.',$draw_result);//将一维数组转换成字符串
        }

        if ($change_status > 0) {
            $user_money = $balance + $draw_add_num;
            $return["return_data"] = array(
                "change_status" => 1,
                "change_money" => $draw_add_num,
                "user_money" => $user_money
            );
        } else {
            $user_money = $balance;
            $return["return_data"] = array(
                "change_status" => -1,
                "change_money" => 0,
                "user_money" => $user_money
            );
        }
        $this->ajaxReturn($return);
    }

    //抽奖扣除积分
    public function reduce_integral(){
        $union = new \MyUnion();

        if (!isset($_SESSION['user'])){
            $return = -1;
        } else {
            $u_id = $_SESSION['user']['uid'];

            if (IS_AJAX) {
                $money = $_POST["money"]; //下注金额
                $balance = $_POST["user_code"]; //用户余额
                $content = "用户抽奖扣除积分";
                $money_is_int = $money/10; //判断是否是整数

                if ($money < 10) {
                    $return = array(
                        "draw_status" => 0,
                        "message" => "下注金额不能小于10积分"
                    );
                    $return['draw_num'] = array();
                } else if ($money > 10000) {
                    $return = array(
                        "draw_status" => 0,
                        "message" => "下注金额不能大于10000积分"
                    );
                    $return['draw_num'] = array();
                } else if ($balance < $money) {
                    $return = array(
                        "draw_status" => 0,
                        "message" => "当前余额小于下注金额，请先充值"
                    );
                } else if(!is_int($money_is_int)) {
                    $return = array(
                        "draw_status" => 0,
                        "message" => "下注金额不是10的倍数"
                    );
                    $return['draw_num'] = array();
                } else {
                    //调用金币减少接口
                    $signature = $union->make_signature_params(); //生成签名参数
                    $union_id = "LYS"; //合作平台id
                    $time = time(); //当前时间戳
                    $url = CARROT_SSO_URL."/users/coins_change?union_id=".$union_id."&time=".$time."&signature=".$signature;
                    $params = "user_id=".$u_id."&type=2&amount=".$money."&content=".$content;
                    $user_coins_data_reduce = json_decode($union->curl($url,$params),true); //扣除接口返回数据
                    if ($user_coins_data_reduce["result"]) {
                        $change_status = -1; //金额变化状态
                        //添加抽奖日志
                        $this->add_log($u_id, $money, 5, $change_status);

                        $user_money = $balance - $money;
                        $return = array(
                            "draw_status" => 5,
                            "user_money" => $user_money
                        );
                    } else {
                        $return["message"] = "扣除积分失败";
                    }
                }
            }
        }

        $this->ajaxReturn($return);
    }

    //抽奖日志
    public function add_log($u_id, $change_money, $draw_status, $change_status){

        $this->if_login();
        $data = array(
            "u_id" => $u_id,
            "change_money" => $change_money,
            "draw_status" => $draw_status,
            "change_status" => $change_status,
            "create_time" => time()
        );

        $m_log = D('log');
        $result = $m_log->add($data);
    }

    //抽奖算法
    public function do_draw($draw_status) {
        $this->if_login();

        if ($draw_status == 4) { //四等奖
            $draw_type = rand(1,3); //四等奖中奖类型  1 (110) 2 (011) 3 (101)
            $num_one = rand(1,19);
            $num_two = $this->get_rand_num($num_one);

            if ($draw_type == 1) {
                $draw_result = array(
                    "0" => $num_one,
                    "1" => $num_one,
                    "2" => $num_two
                );
            } else if ($draw_type == 2) {
                $draw_result = array(
                    "0" => $num_two,
                    "1" => $num_one,
                    "2" => $num_one
                );
            } else {
                $draw_result = array(
                    "0" => $num_one,
                    "1" => $num_two,
                    "2" => $num_one
                );
            }
        } else if ($draw_status == 3) { //三等奖
            $num_one = rand(1,19);
            $draw_result = array(
                "0" => $num_one,
                "1" => $num_one,
                "2" => $num_one
            );
        } else if ($draw_status == 2) { //二等奖
            $num_two = 20;
            $num_one = $this->get_rand_num_two($num_two);

            $draw_result = array(
                "0" => $num_one,
                "1" => $num_one,
                "2" => $num_one
            );
        } else if ($draw_status == 1) { //一等奖
            $num_two = 21;
            $num_one = $this->get_rand_num_two($num_two);

            $draw_result = array(
                "0" => $num_one,
                "1" => $num_one,
                "2" => $num_one
            );
        } else {
            $num_array = range(1,21);
            shuffle($num_array);
            $num = 3;
            $draw_result = array_slice($num_array,0,$num);
        }
        return $draw_result;
    }

    //四等奖生成算法
    public function get_rand_num ($num_one) {
        $num_two = rand(1,19);
        if ($num_two == $num_one) {
            $num_two = $this->get_rand_num($num_one);
        }
        return $num_two;
    }

    //一等奖二等奖生成算法
    public function get_rand_num_two($num_two){
        $num_one = rand(1,21);
        if ($num_two != $num_one) {
            $num_one = $this->get_rand_num_two($num_two);
        }
        return $num_one;
    }

    //退出
    public function logout() {
        session_destroy();		//清除session
    }
}