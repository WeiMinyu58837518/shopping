<?php
namespace Home\Model;
use Think\Model;

class CartModel extends Model{
    //添加购物车
    public function addcart($goods_id,$number,$goods_attr_ids){
        //判断登录状态
        if(cookie("memedaname")){
            $username=cookie("memedaname");
            $user=D("User")->where("username='{$username}'")->find();
            $user_id=$user['id'];
            $where=[
                "user_id"=>$user_id,
                "goods_id"=>$goods_id,
                "goods_attr_ids"=>$goods_attr_ids,
            ];
            $data=$this->where($where)->find();
            if($data){
                $data['number']+=$number;
                $res=$this->save($data);
                return $res !==false;
            }else{
                $data=[
                    "user_id"=>$user_id,
                    "goods_id"=>$goods_id,
                    "goods_attr_ids"=>$goods_attr_ids,
                    "number"=>$number
                ];
                $res=$this->add($data);
                return $res ? true :false;
            }
        }else{
            $data=cookie("cart")?unserialize(cookie("cart")):[];
            $key=$goods_id."-".$goods_attr_ids;
            if(isset($data[$key])){
                $data[$key]+=$number;
            }else{
                $data[$key]=$number;
            }
            $data=serialize($data);
            cookie("cart",$data);
            return true;
        }
    }
    //获取购物车内容
    public function getcart(){
        if(cookie("memedaname")){
            $username=cookie("memedaname");
            $user=D("User")->where("username='{$username}'")->find();
            $user_id=$user['id'];
            $data=$this->where("user_id='{$user_id}'")->select();
            return $data;
        }else{
            $cart=cookie("cart") ? unserialize(cookie("cart")) : [];
            $data=[];
            foreach ($cart as $k => $v){
                $tmp=explode("-", $k);
                $goods_id=$tmp[0];
                $goods_attr_ids=$tmp[1];
                $number=$v;
                $temp=[
                    "goods_id" => $goods_id,
                    "goods_attr_ids" => $goods_attr_ids,
                    "number" => $number
                ];
                $data[]=$temp;
            }
            return $data;
        }
    }

    //购物车购买数量显示
    public function getnumber(){
        if(cookie("memedaname")){
            $data=$this->getcart();
            $totle_num=0;
            foreach($data as $k => $v){
                $totle_num+=$v["number"];
            }
            return $totle_num;
        }else{
            $tmp=unserialize(cookie("cart"));
            $totle_num=0;
            foreach ($tmp as $k =>$v ) {
                $totle_num+=$v;
            }
        }
        return $totle_num;
    }

    //将cookie中的购物车数据存储进数据表
    public function cookieTodb(){
        if(cookie("cart")){
            $tmp=unserialize(cookie("cart"));
        }else{
            $tmp=[];
        }
        foreach ($tmp as $k => $v) {
            $temp=explode("-", $k);
            $goods_id=$temp[0];
            $goods_attr_ids=$temp[1];
            $number=$v;
            $this->addcart($goods_id, $number, $goods_attr_ids);
        }



    }
}