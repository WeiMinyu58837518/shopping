<?php

namespace Admin\Controller;

use Admin\Model\GoodsModel;
use Think\Think;

class GoodsController extends CommonController
{
    //商品添加
    public function goods_add()
    {
        if (IS_POST) {
            $data = I("post.");
            if($data['type_id']==0){
                $error=[
                  "code" => 10,
                  "error" => "商品类型不正确"
                ];
                $this->ajaxReturn($error);
            }
            //保存富文本编辑器内容
            $data['introduce'] = I("post.introduce", "", "kill_xss");
            //商品logo上传
            if (!session("?goods_big_img")) {
                $return = [
                    "code" => 9,
                    "error" => "您没有上传商品logo"
                ];
                $this->ajaxReturn($return);
            }
            $data['goods_big_img'] = session("goods_big_img");
//            if ($_FILES['logo'] && $_FILES['logo']['error'] == 0) {
//                $config = [
//                    "exts" => ["jpg", "png", "gif", "jpeg","bmp"],
//                    'rootPath' => ROOT_PATH . UPLOAD_PATH
//                ];
//                $upload = new \Think\Upload($config);
//                $up_res = $upload->uploadOne($_FILES['logo']);
//                //判断是否上传成功
//                if (!$up_res) {
//                    $this->error($upload->getError());
//                } else {
//                    $data["goods_big_img"] = UPLOAD_PATH . $up_res['savepath'] . $up_res['savename'];

            //生成缩略图
            $image = new \Think\Image;
            $image->open(ROOT_PATH . $data["goods_big_img"]);
            $image->thumb(240, 240);
//                    $small_img = UPLOAD_PATH . $up_res['savepath'] . "small_" . $up_res['savename'];
            $small_img = UPLOAD_PATH . "Logo/small/small_" . session("img_name");
            $image->save(ROOT_PATH . $small_img);
            $data['goods_small_img'] = $small_img;
            session("goods_big_img", null);
            session("img_name", null);
//                }
//            }
            //储存进数据表
            $linshi = [
                "name" => $data['name'],
                "price" => $data['price'],
                "number" => $data['number'],
                "cate_id" => $data['cate_id'],
                "introduce" => $data['introduce'],
                "type_id" => $data['type_id'],
                "goods_big_img" => $data['goods_big_img'],
                "goods_small_img" => $data['goods_small_img']
            ];
            $model = D("Goods");
            $re = $model->create($linshi);
            if (!$re) {
                $result = [
                    "code" => 10,
                    "error" => $model->getError()
                ];
                $this->ajaxReturn($result);
            }
            $res = $model->add();
            $idd = $res;

            if ($res) {
                //添加商品类型
                $yanzheng = true;
                foreach ($data["attr_value"] as $k => $v) {
                    foreach ($v as $value) {
                        $row['goods_id'] = $res;
                        $row['attr_id'] = $k;
                        $row['attr_value'] = $value;
                        $r = D("GoodsAttr")->add($row);
                        if (!$r) {
                            $yanzheng = false;
                        }
                    }
                }
                if (!$yanzheng) {
                    $result = [
                        "code" => 7,
                        "error" => "商品类型添加失败"
                    ];
                    $this->ajaxReturn($result);
                }
                //添加成功
                $result = [
                    "code" => 1,
                    "error" => $idd
                ];
                $this->ajaxReturn($result);
            } else {
                //添加失败
                $result = [
                    "code" => 2,
                    "error" => "添加失败"
                ];
                $this->ajaxReturn($result);
            }


        } else {
            $type = D("Type")->select();
            $this->assign("type", $type);
            $cate = D("Category")->select();
            $this->assign("cate", $cate);
            $this->assign("username", cookie('user'));
            $this->display();
        }

    }

    //根据type_id获取属性信息
    public function getattr()
    {
        $type_id = I("post.type_id", 0, "intval");
        if ($type_id < 0) {
            $return = [
                "code" => 5
            ];
            $this->ajaxReturn($return);
        }
        if($type_id == 0){
            $return=[
                "code" => 10
            ];
            $this->ajaxReturn($return);
        }
        $data = D("Attribute")->where("type_id={$type_id}")->select();
        if ($data) {
            $return = [
                "code" => 1,
                "msg" => "success",
                "data" => $data
            ];
            $this->ajaxReturn($return);
        } else {
            $return = [
                "code" => 2,
                "msg" => "field"
            ];
            $this->ajaxReturn($return);
        }
    }


    //商品详情
    public function goods_detail()
    {
        $id = I("get.id");
        $data = D("Goods")->where("id={$id}")->find();
        $this->assign("data", $data);
        $this->assign("username", cookie('user'));
        $this->display();
    }

    //商品编辑
    public function goods_edit()
    {
        if (IS_POST) {
            $data = I("post.");
            //保存富文本编辑器的内容
            $data['introduce'] = I("post.introduce", '', "kill_xss");
            //商品logo上传
            if (session("?goods_big_img")) {
                $data['goods_big_img'] = session("goods_big_img");
                $yaoshanchu = D("Goods")->where(["id" => $data['id']])->find();
                $bigphoto = $yaoshanchu['goods_big_img'];
                $smallphoto = $yaoshanchu['goods_small_img'];

//            if ($_FILES['logo'] && $_FILES['logo']['error'] == 0) {
//                $config = [
//                    "exts" => ["jpg", "png", "gif", "jpeg"],
//                    'rootPath' => ROOT_PATH . UPLOAD_PATH
//                ];
//                $upload = new \Think\Upload($config);
//                $res = $upload->uploadOne($_FILES['logo']);
//                if (!$res) {
//                    $this->error($upload->getError());
//                } else {
//                    $data['goods_big_img'] = UPLOAD_PATH . $res['savepath'] . $res['savename'];

                //生成缩略图
                $image = new \Think\Image;
                $image->open(ROOT_PATH . $data['goods_big_img']);
                $image->thumb(240, 240);
//          $small_img = UPLOAD_PATH . $res['savepath'] . "small_" . $res['savename'];
                $small_img = UPLOAD_PATH . "Logo/small/small_" . session("img_name");
                $image->save(ROOT_PATH . $small_img);
                $data['goods_small_img'] = $small_img;
                session("goods_big_img", null);
                session("img_name", null);



                //商品属性编辑
                $goods_id=$data['id'];
                D("GoodsAttr")->where("goods_id={$goods_id}")->delete();
                foreach ($data['attr_value'] as $k =>$v ) {
                    foreach ($v as $m ) {
                        $row['goods_id']=$goods_id;
                        $row['attr_id']=$k;
                        $row['attr_value']=$m;
                        D("GoodsAttr")->add($row);
                    }
                }
                //商品信息编辑
                $model = D("Goods");
                $res = $model->create($data);
                if (!$res) {
                    $this->ajaxReturn($model->getError());
                }
                $jieguo=$model->where("id={$data['id']}")->save();
                if($jieguo){
                    unlink(ROOT_PATH . $bigphoto);
                    unlink(ROOT_PATH . $smallphoto);
                }
                    $this->ajaxReturn(1);

            } else {
//                }

//            }
                //商品属性编辑
                $goods_id=$data['id'];
                D("GoodsAttr")->where("goods_id={$goods_id}")->delete();
                foreach ($data['attr_value'] as $k =>$v ) {
                    foreach ($v as $m ) {
                        $row['goods_id']=$goods_id;
                        $row['attr_id']=$k;
                        $row['attr_value']=$m;
                        D("GoodsAttr")->add($row);
                    }
                }
                //商品信息编辑
                $model = D("Goods");
                $res = $model->create($data);
                if (!$res) {
                    $this->ajaxReturn($model->getError());
                }
                $model->where("id={$data['id']}")->save();
                $this->ajaxReturn(1);
            }
        } else {
            $id = I("get.id");
            if(empty($id)){
                $this->error("系统错误");
            }
            //获取商品信息
            $data = D("Goods")->where(["id" => $id])->find();
            $this->assign("data", $data);
            //获取商品属性
            $shangpin_attr=D("GoodsAttr")->where("goods_id={$id}")->select();
            //遍历所选商品的类型所有的属性
            $shangpin_type=D("Attribute")->where("type_id={$data['type_id']}")->select();
            $str='';
            foreach($shangpin_type as $k => $v){
                $str.="<label>{$v['attr_name']}</label>";
                if($v['attr_input_type']==0){
                    foreach($shangpin_attr as $x => $y){
                        if($y['attr_id']==$v['attr_id']){
                            $str.="<input type='text' name='attr_value[{$v['attr_id']}][]' value='{$y['attr_value']}'>";
                        }
                    }
                }elseif($v['attr_input_type']==1){
                    $str.='<select name="attr_value['.$v['attr_id'].'][]">';
                    foreach(explode(",",$v['attr_values']) as $a => $b){
                        $str.="<option value='{$b}' ";
                        foreach($shangpin_attr as $q => $w){
                            if($w['attr_id']==$v['attr_id']){
                               if($w['attr_value']==$b){
                                   $str.="selected";
                                }
                            }
                        }
                        $str.=">{$b}</option>";
                    }
                    $str.="</select>";
                }else{
                    foreach (explode(",",$v['attr_values']) as $c => $d ) {
                        $str.='<input type="checkbox" value="'.$d.'" name="attr_value[' . $v['attr_id'] . '][]"';
                        foreach($shangpin_attr as $r => $t){
                            if($t['attr_id']==$v['attr_id']){
                                if($t['attr_value']==$d){
                                    $str.=" checked='checked'";
                                }
                            }
                        }
                        $str.=">{$d}";
                    }
                }
            }
            $this->assign("str",$str);
            //获取所有商品类型
            $type= D("Type")->select();
            $this->assign("type",$type);
            //获取所有商品属性设置项
            $attribute=D("Attribute")->select();
            $this->assign("attribute",$attribute);
            $this->assign("username", cookie('user'));
            $this->display();
        }

    }

    //商品列表
    public function goods_list()
    {
        $model = D('Goods');
        //分页
        //获取总记录数
        $total = $model->count();
        //设置每页显示条数
        $pagesize = 5;
        $page = new \Think\Page($total, $pagesize);
        //分页显示设置
        $page->rollPage = 5;
        $page->lastSuffix = false;
        $page->setConfig("header", "<span class='rows'>共 %TOTAL_ROW% 条记录 么么哒~</span>");
        $page->setConfig("prev", "上一页");
        $page->setConfig("next", "下一页");
        $page->setConfig("first", "首页");
        $page->setConfig("last", "尾页");
        $page->setConfig("theme", '%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');

        //获取分页栏的代码
        $page_html = $page->show();
        //展示分页栏html代码
        $this->assign("page_html", $page_html);
        //当前页数据
        $data = $model->limit($page->firstRow, $page->listRows)->select();
        $this->assign("data", $data);
        $this->assign("username", cookie('user'));
        $this->display();
    }

    //商品删除
    public function goods_delete()
    {
        $id = I("get.id");
        $shanchu = D("Goods")->where("id={$id}")->find();
        $shanchubig = $shanchu['goods_big_img'];
        $shanchusmall = $shanchu['goods_small_img'];
        $shanchuphoto = D("Goodspics")->where("goods_id={$id}")->select();
        $res = D("Goods")->where("id={$id}")->delete();
        if ($res) {
            unlink(ROOT_PATH . $shanchubig);
            unlink(ROOT_PATH . $shanchusmall);
            foreach ($shanchuphoto as $k => $v) {
                unlink(ROOT_PATH . $v["pics_origin"]);
                unlink(ROOT_PATH . $v["pics_big"]);
                unlink(ROOT_PATH . $v["pics_mid"]);
                unlink(ROOT_PATH . $v["pics_sma"]);
            }
            D("Goodspics")->where("goods_id={$id}")->delete();
            $this->ajaxReturn(1);
        } else {
            $this->ajaxReturn(2);
        }
    }

    //图片上传
    public function img()
    {
        //允许上传文件格式
        $typeArr = array("jpg", "png", "gif", "ico");
        //上传路径
        if (isset($_POST)) {

            $name = $_FILES['file']['name'];
            $size = $_FILES['file']['size'];
            $name_tmp = $_FILES['file']['tmp_name'];
            if (empty($name)) {
                echo json_encode(array("error" => "您还未选择图片"));
                exit;
            }
            $type = strtolower(substr(strrchr($name, '.'), 1));
            //获取文件类型

            if (!in_array($type, $typeArr)) {
                echo json_encode(array("error" => "请上传jpg,png或gif类型的图片！"));
                exit;
            }
            if ($size > (500 * 1024 * 1024 * 1024)) {
                echo json_encode(array("error" => "图片大小已超过1gb！"));
                exit;
            }

            $pic_name = time() . rand(10000, 99999) . "." . $type;
            //图片名称
            $time = date("Ymd");
            if (!file_exists(ROOT_PATH . UPLOAD_PATH . "Logo/" . $time)) {
                mkdir(ROOT_PATH . UPLOAD_PATH . "Logo/" . $time);
            };
            $path = ROOT_PATH . UPLOAD_PATH . "Logo/" . $time . "/";
            $pic_url = $path . $pic_name;
            //上传后图片路径+名称
            if (move_uploaded_file($name_tmp, $pic_url)) {//临时文件转移到目标文件夹
                echo json_encode(array("error" => "0", "pic" => $pic_url, "name" => $pic_name));
                $big_img = UPLOAD_PATH . "Logo/" . $time . "/" . $pic_name;
                $img_name = $pic_name;
            } else {
                echo json_encode(array("error" => "上传有误，请检查服务器配置！"));
            }
            session("goods_big_img", $big_img);
            session("img_name", $img_name);

        }
    }

    //商品相册添加
    public function goods_xiangce()
    {
        if (IS_POST) {
            $yanzheng = true;
            $papa = I("post.id");
            $data["goods_id"] = $papa;
            if (!session("?lujing")) {
                $this->ajaxReturn(9);
            }
            if (!session("?mingcheng")) {
                $this->ajaxReturn(9);
            }
            $lujing = session("lujing");
            $mingcheng = session("mingcheng");
            session("lujing", null);
            session("mingcheng", null);
            $image = new \Think\Image();
            foreach ($lujing as $key => $val) {
                $data['pics_origin'] = $val;

                $image->open(ROOT_PATH . $val);
                $image->thumb(800, 800);
                $pics_big = UPLOAD_PATH . "Photo/big/big_" . $mingcheng[$key];
                $image->save(ROOT_PATH . $pics_big);
                $data["pics_big"] = $pics_big;

                $image->thumb(350, 350);
                $pics_mid = UPLOAD_PATH . "Photo/mid/mid_" . $mingcheng[$key];
                $image->save(ROOT_PATH . $pics_mid);
                $data["pics_mid"] = $pics_mid;

                $image->thumb(50, 50);
                $pics_sma = UPLOAD_PATH . "Photo/sma/sma_" . $mingcheng[$key];
                $image->save(ROOT_PATH . $pics_sma);
                $data["pics_sma"] = $pics_sma;

                $res = D("Goodspics")->add($data);
                if (!$res) {
                    $yanzheng = false;
                }

            }
            if ($yanzheng) {
                $this->ajaxReturn(1);
            } else {
                $this->ajaxReturn(2);
            }
        } else {
            session("lujing", null);
            session("mingcheng", null);
            $id = I("get.id");
            $this->assign("data", $id);
            $this->display();
        }

    }

    //测试用方法
    public function demo()
    {
        dump(session("lujing"));
        dump(session("mingcheng"));
    }

    //储存商品相册
    public function goods_img()
    {
        //允许上传文件格式
        $typeArr = array("jpg", "png", "gif", "ico");
        //上传路径

        if (isset($_POST)) {

            $name = $_FILES['file']['name'];
            $size = $_FILES['file']['size'];
            $name_tmp = $_FILES['file']['tmp_name'];
            if (empty($name)) {
                echo json_encode(array("error" => "您还未选择图片"));
                exit;
            }
            $type = strtolower(substr(strrchr($name, '.'), 1));
            //获取文件类型

            if (!in_array($type, $typeArr)) {
                echo json_encode(array("error" => "请上传jpg,png或gif类型的图片！"));
                exit;
            }
            if ($size > (500 * 1024 * 1024 * 1024)) {
                echo json_encode(array("error" => "图片大小已超过1gb！"));
                exit;
            }

            $pic_name = time() . rand(10000, 99999) . "." . $type;
            //图片名称
            $time = date("Ymd");
            if (!file_exists(ROOT_PATH . UPLOAD_PATH . "Photo/" . $time)) {
                mkdir(ROOT_PATH . UPLOAD_PATH . "Photo/" . $time);
            };
            $path = ROOT_PATH . UPLOAD_PATH . "Photo/" . $time . "/";
            $pic_url = $path . $pic_name;
            //上传后图片路径+名称
            if (move_uploaded_file($name_tmp, $pic_url)) {//临时文件转移到目标文件夹
                echo json_encode(array("error" => "0", "pic" => $pic_url, "name" => $pic_name));
                $big_img = UPLOAD_PATH . "Photo/" . $time . "/" . $pic_name;
                $img_name = $pic_name;
            } else {
                echo json_encode(array("error" => "上传有误，请检查服务器配置！"));
            }
            $a = session("lujing") ? session("lujing") : [];
            $a[] = $big_img;
            session('lujing', $a);
            $b = session("mingcheng") ? session("mingcheng") : [];
            $b[] = $img_name;
            session('mingcheng', $b);
        }
    }

    //商品相册管理
    public function goodspics(){
        if(IS_POST){
            $delete_id=I("post.delete_id");
            $res=D("Goodspics")->where("id={$delete_id}")->delete();
            if($res){
                $this->ajaxReturn(1);
            }else{
                $this->ajaxReturn(2);
            }
        }else{
            $id=I("get.id");
            $photo=D("Goodspics")->where("goods_id={$id}")->select();
            $this->assign("photo",$photo);
            $this->assign("username", cookie('user'));
            $this->display();
        }
    }
}
