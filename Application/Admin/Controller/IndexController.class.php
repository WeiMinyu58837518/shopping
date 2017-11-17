<?php

namespace Admin\Controller;


class IndexController extends CommonController
{
    public function index()
    {
        $this->assign("username",cookie("user"));
        $this->display();
    }
}