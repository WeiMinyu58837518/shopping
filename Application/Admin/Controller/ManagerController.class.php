<?php

namespace Admin\Controller;


use DOMCharacterData;

use SimpleXMLElement;

class ManagerController extends CommonController
{
    //管理员新增
    public function manager_add()
    {
        if(IS_POST){
            $data=I("post.");
            $m=D("Manager");
            $res=$m->create($data);
            if(!$res){
                $this->ajaxReturn($m->getError());
            }
            $h=$m->add();
            if($h){
                $this->ajaxReturn(1);
            }else{
                $this->ajaxReturn(2);
            }
        }else{
            $this->assign("username",cookie("user"));
            $this->display();
        }

    }
    //管理员列表
    public function manager_list()
    {
        $data=D("Manager")->select();
        $this->assign("username",cookie("user"));
        $this->assign("data",$data);
        $this->display();
    }
    //管理员编辑
    public function manager_edit()
    {
        if(IS_POST){
            $data=I("post.");
            $model=D("Manager");
            $res=$model->create($data);
            if(!$res){
                $this->ajaxReturn($model->getError());
            }
            $result=$model->where("id={$data['id']}")->save();
            if($result!==0){
                $this->ajaxReturn(1);
            }else{
                $this->ajaxReturn(2);
            }
        }else{
            $id=I("get.id");
            $data=D("Manager")->where("id={$id}")->find();
            if(!$data){
                $this->error("没有这个用户");
            }
            $this->assign("data",$data);
            $this->display();
        }

    }
    //管理员密码修改
    public function changepwd(){
        if(IS_POST){
            $user=I("post.");
            //验证码验证
            $verify=new\Think\Verify();
            $check=$verify->check($user['yanzheng']);
            if(!$check){
                $this->ajaxReturn(1);
            }
            //两次输入密码验证
            if($user['newpwd_2']!=$user['newpwd_1']){
                $this->ajaxReturn(7);
            }
            $res=D("Manager")->where("username='{$user['username']}'")->find();
            if($res){
                if(md666($user['password'])==$res['password']){
                    //这里进行主要工作
                    $data['password']=md666($user['newpwd_2']);
                    $jieguo=D("Manager")->where("username='{$user['username']}'")->save($data);
                    if($jieguo){
                        cookie("user",null);
                        $this->ajaxReturn(6);
                    }else{
                        $this->ajaxReturn(10);
                    }
                }else{
                    $this->ajaxReturn(3);
                }
            }else{
                $this->ajaxReturn(3);
            }
        }else{
            $this->assign("user_name",cookie("user"));
            $this->display();
        }
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

    //管理员删除
    public function manager_delete(){
        $id=I("get.id");
        $result=D("Manager")->where("id={$id}")->find();
        if($result['status']==1){
            $this->ajaxReturn(3);
        }
        $res=D("Manager")->where("id={$id}")->delete();
        if($res){
            $this->ajaxReturn(1);
        }else{
            $this->ajaxReturn(2);
        }
    }

    //重置管理员密码
    public function manager_password_res(){
        $id=I("get.id");
        $data['password']=md666(1);
        $res=D("Manager")->where("id={$id}")->save($data);
        if($res!==0){
            $this->ajaxReturn(1);
        }else{
            $this->ajaxReturn(2);
        }
    }
    //角色列表
    public function manager_name(){
        $data=D("Role")->where("role_id>1")->select();
        $this->assign("data",$data);
        $this->assign("username",cookie("user"));
        $this->display();
    }

    //分派权限显示与设置
    public function manager_setauth(){
        if(IS_POST){
            $data=I("post.");
            $row['role_id']=$data['id'];
            $row['role_auth_ids']=implode(',', $data['ids']);
            $auth=D("Auth")->where("id in ({$row['role_auth_ids']})")->select();
            foreach ($auth as $k => $v) {
                if($v['pid']>0){
                    $row['role_auth_ac'].=$v['auth_c']."-".$v['auth_a'].",";
                }
            }
            $row['role_auth_ac']=rtrim($row['role_auth_ac'],",");
            $res=D("Role")->where("role_id={$row['role_id']}")->save($row);
            if($res !==0){
                $this->ajaxReturn(1);
            }else{
                $this->ajaxReturn(2);
            }

        }else{
            $id=I("get.id");
            //页面角色名展示
            $data=D("Role")->where("role_id={$id}")->find();
            $this->assign("data",$data);
            //顶级权限显示
            $auth_top=D("Auth")->where("pid=0")->select();
            $this->assign("auth_top",$auth_top);
            //二级权限显示
            $auth_second=D("Auth")->where("pid>0")->select();
            $this->assign("auth_second",$auth_second);
            //传递id参数
            $this->assign("id",$id);
            $this->assign("username",cookie("user"));
            $this->display();
        }
    }
    //角色删除
    public function manager_auth_delete(){
        $id=I("get.id");
        if($id==1){
            $this->ajaxReturn([error=>5,re=>"超级管理员不能删除"]);
        }
        $res_1=D("Role")->where("role_id={$id}")->delete();
        if($res_1){
            $res_2=D("Manager")->where("role_id={$id}")->delete();
            $this->ajaxReturn([error=>1,re=>$res_2]);
        }else{
            $this->ajaxReturn([error=>5,re=>"角色删除失败"]);
        }

    }
}