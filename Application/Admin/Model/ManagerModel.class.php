<?php
namespace Admin\Model;

use Think\Model;

class ManagerModel extends Model
{
    //自动完成
    protected $_auto = [
//        ["update_time", "date('Y-m-d H:i:s')", 1, "function"]
    ];
    //自动验证
    protected $_validate = [
        ['username', "require", "用户名不能为空", 0, "regex", "3"],
        ['password', "require", "密码不能为空", 0, "regex", "3"],
        ['email', "email", "邮箱格式不正确", 0, "regex", "3"],
        ['nickname', "require", "昵称不能为空", 0, "regex", "3"]
        ];
}
