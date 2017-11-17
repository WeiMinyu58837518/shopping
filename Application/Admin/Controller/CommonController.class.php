<?php
namespace Admin\Controller;
use Think\Controller;
class CommonController extends Controller{
    public function __construct()
    {
        parent::__construct();
        //进行是否登陆的判断
        if(!cookie("user")){
            session("url",__SELF__);
            $this->error("请登录",U("Admin/Login/login"),1);
        }
        //进行管理员权限控制
        $this->checkmanager();
        //查询左侧菜单权限
        $this->getnav();
    }
    //查询权限方法
    public function getnav(){
        if(!session("?top") || !session("?second")){
            $username=cookie("user");
            $res=D("Manager")->where("username='{$username}'")->find();
            $role_id=$res["role_id"];
            if($role_id==1){
                $top=D("Auth")->where("pid=0 and is_nav=1")->select();
                $second=D("Auth")->where("pid>0 and is_nav=1")->select();
            }else{
                $role_auth=D("Role")->where("role_id={$role_id}")->find();
                $role_auth_ids=$role_auth['role_auth_ids'];
                $top=D("Auth")->where("pid=0 and is_nav=1")->select();
                $second=D("Auth")->where("pid>0 and id in({$role_auth_ids}) and is_nav=1")->select();
            }
            session("top",$top);
            session("second",$second);
        }
        }
    //对管理员进行权限控制
    public function checkmanager(){
        $username=cookie("user");
        $res=D("Manager")->where("username='{$username}'")->find();
        $role_id=$res["role_id"];
        if($role_id==1){
            return;
        }else{
            $role=D("Role")->where("role_id={$role_id}")->find();
            $c=CONTROLLER_NAME;
            $a=ACTION_NAME;
            //判断是否是首页，不需要检测权限
            if($c=="Index" && $a=="index"){
                return;
            }

            //将控制器名称与方法名称用-拼接，代表权限
            $ac=$c."-".$a;
            $role_auth_ac=explode(",", $role["role_auth_ac"]);
            if(!in_array($ac, $role_auth_ac)){
                $this->error("抱歉，您没有此权限，如有问题请与管理员联系","",1);
//                $this->redirect("Admin/Index/index");
            }
        }
    }
}