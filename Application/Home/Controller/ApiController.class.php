<?php
namespace Home\Controller;
use Think\Controller;
class ApiController extends Controller{
    public function duanxin(){
        $phone=I("get.phone");
        if(empty($phone)){
            $error=[
                "code" => 10001
            ];
            $this->ajaxReturn($error);
        }
        $yanzhengma=mt_rand(1000,9999);
        $content="【传智播客】您用于注册的验证码为{$yanzhengma}，如非本人操作，请忽略本短信。";
        $appkey="56094ca4632daa86455f007d61e3b113";
        $url="http://115.28.177.129:70/msgapi.php?mobile={$phone}&content={$content}&appkey={$appkey}";
        $res=curl_request($url,false,[],false);
        $arr=json_decode($res,true);
        $error=[
            "code" => 10000,
            "er" => $arr
        ];
        $this->ajaxReturn($error);

    }
    public function qq_login(){
        require_once "/Application/Tools/qq/API";
        $qc=new \QC();
        $access_token=$qc->qq_callback();
        $openid=$qc->get_openid();
        $qc=new \QC($access_token,$openid);
        $info=$qc->get_user_info();
        $nickname=$info['nickname'];
        $data=[
            "username" => $nickname,
            "openid" => $openid
        ];
        $user=D("User")->where("openid={$openid}")->find();
        if($user){
            D("User")->where("openid={$openid}")->save($data);
        }else{
            $data['create_time']=time();
            D("User")->add($data);
        }
        cookie("memedaname",$data['username']);
        echo "<script>window.close();window.opener.location.href='/';</script>";
    }
}