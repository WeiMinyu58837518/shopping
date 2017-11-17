<?php
namespace Home\Model;
use Think\Model;
class UserModel extends Model{
    protected $_validate = [
        ['username', "require", "用户名不能为空", 0, "regex", "3"],
        ['phone', "require", "手机不能为空", 0, "regex", "3"],
        ['password', "require", "密码不能为空", 0, "regex", "3"],
        ['email', "email", "邮件格式不正确", 0, "regex", "3"],
        ['email', "require", "邮箱不能为空", 0, "regex", "3"],
        ['email', "", "邮箱重复", 0, "unique", "3"],
        ["phone","","手机号重复",0,"unique","3"]
    ];
    protected $_auto = [
        ["password","md666",1,"function"],
        ["create_time","time",1,"function"]
    ];
}