<?php

namespace Home\Controller;

use Think\Controller;

class UserController extends Controller
{
    //登录
    public function login()
    {
        if (IS_POST) {
            $data=I("post.");
            $chk=I("get.chk");
            $res=D("User")->where("username='{$data['user']}'")->find();
            if(!$res){
                $error=[
                    "code" => 2,
                    "er" => "没有这个账号"
                ];
                $this->ajaxReturn($error);
            }
            if($res['password']===md666($data['password'])){
                $error=[
                    "code" => 1,
                    "er"=>U("Home/Index/index")
                ];
                if($chk==1){
                    cookie("memedaname",$data['user'],3600);
                    cookie("memedapassword",$data['password'],3600);
                }else{
                    cookie("memedaname",$data['user']);
                }
                if(session("?uu")){
                    $error['er']=U(session("uu"));
                }
                D("Cart")->cookieTodb();
                $this->ajaxReturn($error);
            }else{
                $error=[
                    "code" => 3,
                    "er" => "账号或密码错误"
                ];
                $this->ajaxReturn($error);
            }
        } else {
            layout(false);
            $this->assign("title", "登录");
            $this->display();
        }

    }

    //注册
    public function register()
    {
        if (IS_POST) {
            $data = I("post.");
            $chk = I("get.chk");
            if ($chk == 1) {
                if ($data['password_1'] !== $data['password_2']) {
                    $error = [
                        "code" => 4,
                        "er" => "两次输入密码不一致"
                    ];
                    $this->ajaxReturn($error);
                }
                $zhuce = [
                    "username" => $data['email'],
                    "email" => $data['email'],
                    "password" => $data['password_1']
                ];
                $user = D("User");
                $res = $user->create($zhuce);
                if (!$res) {
                    $error = [
                        "code" => 3,
                        "er" => $user->getError()
                    ];
                    $this->ajaxReturn($error);
                }
                $result = $user->add();
                if ($result) {
                    $error = [
                        "code" => 1,
                        "er" => "注册成功"
                    ];
                    $this->ajaxReturn($error);
                } else {
                    $error = [
                        "code" => 2,
                        "er" => "注册失败"
                    ];
                    $this->ajaxReturn($error);
                }
            }
            if ($chk == 2) {
                if ($data['password_3'] !== $data['password_4']) {
                    $error = [
                        "code" => 4,
                        "er" => "两次输入密码不一致"
                    ];
                    $this->ajaxReturn($error);
                }
                $zhuce = [
                    "username" => $data['phone'],
                    "phone" => $data['phone'],
                    "password" => $data['password_3']
                ];
                $user = D("User");
                $res = $user->create($zhuce);
                if (!$res) {
                    $error = [
                        "code" => 3,
                        "er" => $user->getError()
                    ];
                    $this->ajaxReturn($error);
                }
                $result = $user->add();
                if ($result) {
                    $error = [
                        "code" => 1,
                        "er" => "注册成功"
                    ];
                    $this->ajaxReturn($error);
                } else {
                    $error = [
                        "code" => 2,
                        "er" => "注册失败"
                    ];
                    $this->ajaxReturn($error);
                }
            }
            if ($chk != 1 && $chk != 2) {
                $error = [
                    "code" => 5,
                    "error" => "非法参数"
                ];
                $this->ajaxReturn($error);
            }
            $this->ajaxReturn($data);
        } else {
            layout(false);
            $this->assign("title", "注册");
            $this->display();
        }

    }


    //退出
    public function tuichu(){
        cookie("memedaname",null);
        $this->ajaxReturn(1);
    }


}