<?php
namespace Admin\Controller;

class AuthController extends CommonController{
    public function auth_list(){
        $this->assign("username",cookie("user"));
        $this->display();
    }
    public function auth_add(){
        if(IS_POST){

        }else{
            $this->assign("username",cookie("user"));
        }
        $this->display();
    }
}