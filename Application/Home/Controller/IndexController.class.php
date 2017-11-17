<?php

namespace Home\Controller;

use Think\Controller;

class IndexController extends Controller
{
    public function index()
    {
        $data=D("Goods")->select();
        $this->assign("data",$data);
        $cate=D("Category")->select();
        $this->assign("cate",$cate);
        $this->assign("title","首页");
        $this->display();
    }
    public function detail(){
        $id=I("get.id",0,"intval");
        if($id<=0){
            $this->error("参数不正确");
        }
        //商品名称、价格、详情展示
        $goods=D("Goods")->where("id={$id}")->find();
        $this->assign("goods",$goods);
        //商品大图展示
        $pics=D("Goodspics")->where("goods_id={$id}")->select();
        $this->assign("pics",$pics);
        //商品大类属性展示
        $attribute=D("Attribute")->where("type_id={$goods['type_id']}")->select();
        $this->assign("attribute",$attribute);
        //商品个体详细属性展示
        $goods_attr=D("GoodsAttr")->where("goods_id={$id}")->select();
        $this->assign("goods_attr",$goods_attr);
        $this->assign("title","商品详情");
        $this->display();
    }
    //快递测试
    public function kuaidi(){
        $type="yunda";
        $postid="3101314976598";
        $url="https://www.kuaidi100.com/query?type={$type}&postid={$postid}";
        $res=curl_request($url,false,[],true);
        $arr=json_decode($res,true);
//        dump($arr);
        $arr=$arr["data"];
        $this->assign("data",$arr);
        layout(false);
        $this->display();
    }
    //发送邮件测试
    public function sendMail(){
        $email="xgy58837518@163.com";
        $subject="哈哈哈你小子厉害了";
        $body="哈哈哈你好讨厌呀";
        $res=send_mail($email,$subject,$body);
        echo $res;
    }
}