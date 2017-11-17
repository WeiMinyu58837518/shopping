<?php
namespace Admin\Controller;
class OrderController extends CommonController{
    public function order_list(){
        $order=D("Order")->alias("o1")->field("o1.*,o2.consignee,o2.address,o2.phone")->join("left join tpshop_address as o2 on o1.address_id=o2.id")->select();
//        foreach ($order as $k=>$v) {
//            D("OrderGoods")->where("order_id={$v['id']}")->select();
//        }
//        dump($order);die;
        $this->assign("order",$order);
        $this->assign("username", cookie('user'));
        $this->display();
    }
    public function order_goods(){
        $id=I("get.id");
        $data=D("OrderGoods")->where("order_id={$id}")->select();
        foreach ($data as $k => &$v) {
            $v['goods']=D("Goods")->where("id={$v['goods_id']}")->find();
            $temp=D("GoodsAttr")->where("id in({$v['goods_attr_ids']})")->select();
            foreach ($temp as $x => $y) {
                $tmp=D("Attribute")->where("attr_id={$y['attr_id']}")->find();
                $v['shuxing'].=$tmp['attr_name'].":".$y['attr_value'].";&nbsp&nbsp";
            }
        }
//        dump($data);die();
        $this->assign("data",$data);
        $this->assign("username", cookie('user'));
        $this->display();

    }
}