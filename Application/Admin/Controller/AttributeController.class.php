<?php
namespace Admin\Controller;
class AttributeController extends CommonController{
    //商品属性添加
    public function attribute_add(){
        if(IS_POST){
            $data=I("post.");
            $model=D("Attribute");
            $res=$model->create($data);
            if(!$res){
                $this->ajaxReturn($model->getError());
            }
            $result=$model->add();
            if($result){
                $this->ajaxReturn(1);
            }else{
                $this->ajaxReturn(2);
            }
        }else{
            $type=D("Type")->select();
            $this->assign("type",$type);
            $this->assign("username",cookie("user"));
            $this->display();
        }

    }
    //商品属性列表
    public function attribute_list(){
        $data=D("Attribute")->alias("t1")->field("t1.*,t2.type_name")->join("left join tpshop_type as t2 on t1.type_id=t2.type_id")->select();
        $this->assign("data",$data);
        $this->assign("username",cookie("user"));
        $this->display();
    }
    //商品属性删除
    public function attribute_delete(){
        $id=I("get.id");
        $res=D("GoodsAttr")->where("attr_id={$id}")->find();
        if($res){
            $error=[
                "code" => 5,
                "er" => "该属性正在被某些商品使用，不能删除"
            ];
            $this->ajaxReturn($error);
        }else{
            $result=D("Attribute")->where("attr_id={$id}")->delete();
            if($result){
                $error=[
                    "code" => 1,
                    "er" =>"删除成功"
                ];
                $this->ajaxReturn($error);
            }else{
                $error=[
                    "code" => 2,
                    "er" =>"删除失败"
                ];
                $this->ajaxReturn($error);
            }
        }
    }
}