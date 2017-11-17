<?php

namespace Admin\Model;

use Think\Model;

class GoodsModel extends Model
{
    //字段映射
    protected $_map = [
        "name" => "goods_name",
        "price" => "goods_price",
        "number" => "goods_number",
        "introduce" => "goods_introduce",
    ];
    //自动验证
    protected $_validate = [
        ['goods_name', "require", "商品名称不能为空", 0, "regex", "3"],
        ["goods_price", "require", "商品价格不能为空", 0, "regex", "3"],
        ["goods_price", "currency", "商品价格必须是数字", 0, "regex", "3"],
        ["goods_number", "require", "商品数量未填写", 0, "regex", "3"],
        ["goods_number", "number", "商品数量必须为数字", 0, "regex", "3"],

    ];
    //自动完成
    protected $_auto = [
        ["goods_create_time", "time", 1, "function"]
    ];
}