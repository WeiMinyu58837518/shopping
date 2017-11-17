<?php

namespace Admin\Controller;


use Smarty_Internal_Compile_Assign;

class TypeController extends CommonController
{
    //商品类型新增
    public function type_add()
    {
        if(IS_POST){
            $type_name=I("post.type_name");
            if(empty($type_name)){
                $this->ajaxReturn(6);
            }
            $res=D("Type")->add(["type_name" => $type_name]);
            if($res){
                $this->ajaxReturn(1);
            }else{
                $this->ajaxReturn(2);
            }
        }else{
            $this->display();
        }

    }
    //商品类型列表
    public function type_list()
    {
        $data=D("Type")->select();
        $this->assign("data",$data);
        $this->assign("username",cookie("user"));
        $this->display();
    }
    //商品类型删除
    public function type_delete(){
        $type_id=I("get.type_id");
        $res=D("Type")->where("type_id={$type_id}")->delete();
        if($res){
            $this->ajaxReturn(1);
        }else{
            $this->ajaxReturn(2);
        }
    }
    //商品类型名称修改
    public function type_change(){
        if(IS_POST){
            $data=I("post.");
            if(empty($data['type_name'])){
                $this->ajaxReturn(5);
            }
            $res=D("Type")->where("type_id={$data['type_id']}")->save($data);
            if($res){
                $this->ajaxReturn(1);
            }else{
                $this->ajaxReturn(2);
            }
        }else{
            $type_id=I("get.type_id");
            $data=D("Type")->where("type_id={$type_id}")->find();
            $this->assign("data",$data);
            $this->assign("username",cookie("user"));
            $this->display();
        }
    }
}