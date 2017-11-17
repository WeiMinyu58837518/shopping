<?php

namespace Home\Controller;

use Serializable;
use Think\Controller;

class CartController extends Controller
{
    //添加购物车
    public function addcart()
    {
        if (IS_POST) {
            $data = I("post.");
            if (!$data['goods_id'] || !$data['goods_attr_ids'] || !$data['number']) {
                $this->error("购买信息异常");
            } else {
                $res = D("Cart")->addcart($data['goods_id'], $data['number'], $data['goods_attr_ids']);
                if ($res) {
                    $goods = D("Goods")->where("id={$data['goods_id']}")->find();
                    $this->assign("goods", $goods);
                    layout(false);
                    $this->display();
                } else {
                    $this->error("系统错误");
                }
            }

        } else {
            $this->error("非法操作");
        }
    }

    //购物车显示
    public function cart()
    {
        $data = D("Cart")->getcart();
        foreach ($data as $k => &$v) {
            $goods = D("Goods")->where("id={$v['goods_id']}")->find();
            $v['goods'] = $goods;
            $goods_attr = D("GoodsAttr")->alias("a1")->join("left join tpshop_attribute as a2 on a1.attr_id=a2.attr_id")->where("a1.id in ({$v['goods_attr_ids']})")->select();
            $v['goods_attr'] = $goods_attr;
        }
        $this->assign("data", $data);
        layout(false);
        $this->display();
    }

    //获取购物车中商品的总数
    public function cartnumber()
    {
        $res = D("Cart")->getnumber();
        $this->ajaxReturn($res);
    }

    //购物车中修改商品数量
    public function changenumber()
    {
        $data = I("post.");
        if (cookie("memedaname")) {
            $username=cookie("memedaname");
            $user = D("User")->where("username='{$username}'")->find();
            $user_id = $user['id'];
            $where = [
                "user_id" => $user_id,
                "goods_id" => $data['goods_id'],
                "goods_attr_ids" => $data['goods_attr_ids'],
            ];
            $cunchu=[
                "user_id" => $user_id,
                "goods_id" => $data['goods_id'],
                "goods_attr_ids" => $data['goods_attr_ids'],
                "number" => $data['number']
            ];
            $res = D("Cart")->where($where)->save($cunchu);
            if ($res) {
                $this->ajaxReturn(1);
            } else {
                $this->ajaxReturn(2);
            }
        } else {
            $where = $data['goods_id'] . "-" . $data['goods_attr_ids'];
            $res = unserialize(cookie("cart"));
            $res[$where] = $data['number'];
            $cart=serialize($res);
            cookie("cart",$cart);
            $this->ajaxReturn(1);
        }

    }

    //删除购物车物品
    public function deletecate(){
        $data=I("post.");
        if(cookie("memedaname")){
            $username=cookie("memedaname");
            $user=D("User")->where("username='{$username}'")->find();
            $user_id=$user['id'];
            $where=[
                "user_id" => $user_id,
                "goods_id" => $data['goods_id'],
                "goods_attr_ids" => $data['goods_attr_ids']
            ];
            $res=D("Cart")->where($where)->delete();
            if($res){
                $this->ajaxReturn(1);
            }else{
                $this->ajaxReturn(2);
            }
        }else{
            $res=unserialize(cookie('cart'));
            $k=$data['goods_id']."-".$data['goods_attr_ids'];
            unset($res[$k]);
            cookie("cart",serialize($res));
            $this->ajaxReturn(1);
        }
    }

    //结算页
    public function flow2(){
        //登录判断
        if(cookie("memedaname")){
            $cart_ids=I("get.cart_ids");
            $username=cookie('memedaname');
            $user=D("User")->where("username='{$username}'")->find();
            $user_id=$user['id'];
            $data=D("Cart")->alias("t1")->field("t1.*,t2.goods_price,t2.goods_name,t2.goods_small_img")->join("left join tpshop_goods as t2 on t1.goods_id=t2.id")->where("t1.id in ({$cart_ids})")->select();
            foreach ($data as $k => &$v) {
                $goods_attr=D("GoodsAttr")->alias("a1")->field("a1.*,a2.attr_name")->join("left join tpshop_attribute as a2 on a1.attr_id=a2.attr_id")->where("a1.id in ({$cart_ids})")->select();
                $v['goods_attr']=$goods_attr;
                $data[$k]['jine']=$v['number']*$v['goods_price'];
//                $data[$k]['zongjine']+=$data[$k]['jine'];
            }
            $zongjine=0;
            foreach ($data as $k => &$v) {
                $zongjine+=$v['jine'];
            }
            $this->assign("zongjine",$zongjine);
            $this->assign("data",$data);
            layout(false);
            $this->display();
        }else{
            session("uu","Home/Cart/cart");
            $this->redirect("Home/User/login");
        }
    }

    //付款后页面
    public function flow3(){
        $data=I("post.");
        $username=cookie("memedaname");
        $user=D("User")->where("username='{$username}'")->find();
        $user_id=$user['id'];
        $order_sn=time().mt_rand(10000,99999);
        $address_id=$data['address_id'];
        $shipping_type=$data['shipping_type'];
        $pay_type=$data['pay_type'];
        $create_time=time();
        $pay_status=0;
        $cart=D("Cart")->alias("c1")->field("c1.*,c2.goods_price")->join("left join tpshop_goods as c2 on c1.goods_id=c2.id")->where("c1.id in ({$data['cart_ids']})")->select();
        $order_amount=0;
        foreach($cart as $k => $v){
            $order_amount+=$v['goods_price']*$v['number'];
        }
        $sav=[
            "order_sn" =>$order_sn,
            "order_amount" =>$order_amount,
            "user_id" =>$user_id,
            "address_id" =>$address_id,
            "shipping_type" =>$shipping_type,
            "pay_status" =>$pay_status,
            "pay_type" =>$pay_type,
            "create_time" =>$create_time
        ];
        $res=D("Order")->add($sav);
        if($res){
            foreach($cart as $k => $v){
                $sav=[
                    "order_id" => $res,
                    "goods_id" => $v['goods_id'],
                    "goods_price" => $v['goods_price'],
                    "number" => $v['number'],
                    "goods_attr_ids" => $v['goods_attr_ids']
                ];
                D("OrderGoods")->add($sav);
                D("Cart")->where("id in ({$data['cart_ids']})")->delete();
            }
            layout(false);
            $this->display();
        }else{
            $this->error("系统错误",U("Home/Index/index"));
        }

    }

    //地址页取数据
    public function address(){
        $username=cookie('memedaname');
        $user=D("User")->where("username='{$username}'")->find();
        $user_id=$user['id'];
        $data=D("Address")->where("user_id={$user_id}")->select();
        $this->ajaxReturn($data);
    }

    //新地址存储
    public function saveAddress(){
        $data=I("post.");
        $username=cookie("memedaname");
        $user=D("User")->where("username='{$username}'")->find();
        $data["user_id"]=$user['id'];
        $res=D("Address")->add($data);
        if($res){
            $this->ajaxReturn(10000);
        }else{
            $this->ajaxReturn(20000);
        }
    }

}