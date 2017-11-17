<?php
namespace Admin\Model;
use Think\Model;
class AttributeModel extends Model{
    //自动验证
    protected $_validate = [
        ['attr_name', "require", "类型名称不能为空", 0, "regex", "3"]
    ];
}