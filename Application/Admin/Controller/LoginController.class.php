<?php

namespace Admin\Controller;

use Think\Controller;

class loginController extends Controller
{
    //管理员登录
    public function login(){
        if(IS_POST){
            $user=I("post.");
            //验证码验证
            $verify=new \Think\Verify();
            $check=$verify->check($user['yanzheng']);
            if(!$check){
//                $this->error("验证码错误");
                $this->ajaxReturn(1);
            }else{
                $zhanghao=D("Manager")->where("username='{$user['username']}'")->find();
                if($zhanghao){
                    if(md666($user['password'])==($zhanghao['password'])){
                        cookie('user',$zhanghao['username'],99999);
//                    $this->success("登录成功",U(session("url")));
                        if(session("?url")){
                            $this->ajaxReturn(U(session("url")));
                        }else{
                            $this->ajaxReturn(5);
                        }
                    }else{
//                    $this->error("账号或密码错误");
                        $this->ajaxReturn(2);
                    }
                }else{
//                $this->error("账号或密码错误");
                    $this->ajaxReturn(2);
                }
            }



        }else{
            $this->display();
        }

    }

    //用户退出
    public function tuichu(){
        cookie("user",null);
        session("top",null);
        session("second",null);
        $this->success("退出成功",U("Admin/Login/login"));
    }
    //生成验证码
    public function yanzheng()
    {
        $config = [
            fontSize => 50,
            length => 2,
            useCurve => false,
        ];
        $verify = new \Think\Verify($config);
        $verify->entry();
    }
}