<?php
namespace Home\Controller;
use Think\Controller;

require('sdk/carrot.sso.php');
require('sdk/union.php');
class SizeGameController extends Controller {
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

    //猜大小游戏页面
    public function index(){

        $this->display();
    }



    //执行下注操作
    public function bottom_pour(){

    }
}